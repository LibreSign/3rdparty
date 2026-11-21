// SPDX-FileCopyrightText: 2026 LibreCode coop and contributors
// SPDX-License-Identifier: AGPL-3.0-or-later
'use strict'

const SHA = /^[0-9a-f]{40}$/

function feedbackBody({mode, outcome, runUrl, commitSha, changed}) {
  if (mode === 'start') {
    return '⏳ **/composer-update accepted**\n\nRegenerating Composer dependencies. ' +
      '[Follow this workflow run](' + runUrl + '). This comment will be updated.'
  }
  if (outcome === 'success' && changed === 'true' && SHA.test(commitSha || '')) {
    return '✅ **/composer-update completed**\n\nPublished verified, DCO-signed ' +
      '[commit](https://github.com/LibreSign/3rdparty/commit/' + commitSha + '). ' +
      'The updated PR checks must still pass before merge. [Workflow](' + runUrl + ').'
  }
  if (outcome === 'success' && changed === 'false') {
    return '✅ **/composer-update completed**\n\nNo changes to scoped vendor files ' +
      'were required. Check the existing PR CI before merge. [Workflow](' + runUrl + ').'
  }
  const reason = outcome === 'cancelled' ? 'cancelled' : 'failed'
  return '❌ **/composer-update ' + reason + '**\n\nThe command could not ' +
    'confirm a verified dependency update. Inspect the [workflow logs](' +
    runUrl + ') before retrying. Do not merge while PR checks are failing.'
}

async function sendFeedback({github, context, core, env}) {
  const owner = context.repo.owner
  const repo = context.repo.repo
  const mode = env.STATUS_MODE
  if (!['start', 'finish'].includes(mode) || !/^\d+$/.test(env.RUN_ID || '')) {
    throw new Error('Invalid command feedback context')
  }
  const originalId = context.payload.comment?.id
  const issueNumber = context.issue.number
  if (!Number.isSafeInteger(originalId) || !Number.isSafeInteger(issueNumber)) {
    throw new Error('Missing command comment or pull request number')
  }
  const runUrl = 'https://github.com/' + owner + '/' + repo + '/actions/runs/' + env.RUN_ID
  const outcome = env.INIT_RESULT === 'success' && env.PROCESS_RESULT === 'success'
    ? 'success'
    : (env.INIT_RESULT === 'cancelled' || env.PROCESS_RESULT === 'cancelled'
      ? 'cancelled' : 'failure')
  const body = feedbackBody({mode, outcome, runUrl,
    commitSha: env.COMMIT_SHA, changed: env.CHANGED})
  const previousId = Number(env.STATUS_COMMENT_ID)
  let statusId
  if (mode === 'finish' && Number.isSafeInteger(previousId) && previousId > 0) {
    try {
      await github.rest.issues.updateComment({owner, repo, comment_id:previousId, body})
      statusId = previousId
    } catch (error) {
      if (error.status !== 404) throw error
      core.warning('Previous status comment unavailable; creating a replacement')
    }
  }
  if (!statusId) {
    const response = await github.rest.issues.createComment({
      owner, repo, issue_number:issueNumber, body,
    })
    statusId = response.data.id
  }
  core.setOutput('status_id', String(statusId))

  const reaction = mode === 'start' ? 'eyes' : outcome === 'success' ? '+1' : '-1'
  try {
    await github.rest.reactions.createForIssueComment({
      owner, repo, comment_id:originalId, content:reaction,
    })
  } catch (error) {
    core.warning('Could not add ' + reaction + ' reaction: HTTP ' +
      (error.status || 'unknown') + ' (' + (error.message || 'GitHub API error') + ')')
  }
  if (mode === 'finish') {
    try {
      const {data: reactions} = await github.rest.reactions.listForIssueComment({
        owner, repo, comment_id:originalId, per_page:100,
      })
      const bot = env.FEEDBACK_APP_SLUG ? env.FEEDBACK_APP_SLUG + '[bot]' : 'github-actions[bot]'
      for (const item of reactions) {
        if (item.content === 'eyes' && item.user?.login === bot) {
          await github.rest.reactions.deleteForIssueComment({
            owner, repo, comment_id:originalId, reaction_id:item.id,
          })
        }
      }
    } catch (error) {
      core.warning('Could not remove acknowledgement reaction: HTTP ' +
        (error.status || 'unknown'))
    }
  }
}

module.exports = {feedbackBody, sendFeedback}
