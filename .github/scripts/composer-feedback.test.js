// SPDX-FileCopyrightText: 2026 LibreCode coop and contributors
// SPDX-License-Identifier: AGPL-3.0-or-later
'use strict'

const test = require('node:test')
const assert = require('node:assert/strict')
const {feedbackBody, sendFeedback} = require('./composer-feedback.js')

function fixture(changes = {}) {
  const calls = [], outputs = {}, warnings = []
  const wrap = (name, getData = () => ({})) => async args => {
    calls.push({name, args})
    return {data: getData(args)}
  }
  const github = {rest: {
    issues: {
      createComment: wrap('createComment', () => ({id:371})),
      updateComment: wrap('updateComment'),
    },
    reactions: {
      createForIssueComment: wrap('react'),
      listForIssueComment: wrap('reactions', () => []),
      deleteForIssueComment: wrap('remove'),
    },
    apps: {getAuthenticated: wrap('app', () => ({slug:'librecode-workflow-automation'}))},
  }}
  const context = {repo:{owner:'LibreSign', repo:'3rdparty'}, issue:{number:125},
    payload:{comment:{id:7066693019}}}
  const env = {STATUS_MODE:'start', RUN_ID:'12345', INIT_RESULT:'success',
    PROCESS_RESULT:'success', CHANGED:'true', COMMIT_SHA:'a'.repeat(40), ...changes}
  const core = {setOutput:(k,v) => {outputs[k]=v}, warning:v => warnings.push(v)}
  return {github, context, env, core, calls, outputs, warnings}
}

test('acknowledges a valid command with a run link and eyes reaction', async () => {
  const f = fixture()
  await sendFeedback(f)
  assert.match(f.calls.find(x => x.name === 'createComment').args.body,
    /actions\/runs\/12345/)
  assert.equal(f.calls.find(x => x.name === 'react').args.content, 'eyes')
  assert.equal(f.outputs.status_id, '371')
})

test('updates existing bot comment rather than posting duplicates', async () => {
  const f = fixture({STATUS_MODE:'finish', STATUS_COMMENT_ID:'371'})
  await sendFeedback(f)
  assert.equal(f.calls.filter(x => x.name === 'createComment').length, 0)
  assert.match(f.calls.find(x => x.name === 'updateComment').args.body,
    /checks must still pass/)
  assert.equal(f.calls.find(x => x.name === 'react').args.content, '+1')
})

test('reports failure with logs and thumbs down', async () => {
  const f = fixture({STATUS_MODE:'finish', STATUS_COMMENT_ID:'371',
    PROCESS_RESULT:'failure'})
  await sendFeedback(f)
  assert.match(f.calls.find(x => x.name === 'updateComment').args.body,
    /workflow logs/)
  assert.equal(f.calls.find(x => x.name === 'react').args.content, '-1')
})

test('reports a blocked init job even without an acknowledgement', async () => {
  const f = fixture({STATUS_MODE:'finish', INIT_RESULT:'failure', PROCESS_RESULT:'skipped'})
  await sendFeedback(f)
  assert.equal(f.calls.filter(x => x.name === 'createComment').length, 1)
  assert.equal(f.calls.find(x => x.name === 'react').args.content, '-1')
})

test('a failed reaction does not suppress the visible status comment', async () => {
  const f = fixture()
  f.github.rest.reactions.createForIssueComment = async () => {
    const e = Error('Forbidden');e.status=403;throw e
  }
  await sendFeedback(f)
  assert.equal(f.calls.filter(x => x.name === 'createComment').length, 1)
  assert.match(f.warnings.join(' '), /HTTP 403/)
})

test('missing acknowledgement is replaced rather than leaving no status', async () => {
  const f = fixture({STATUS_MODE:'finish', STATUS_COMMENT_ID:'999'})
  f.github.rest.issues.updateComment = async () => {
    const e = Error('Not found');e.status=404;throw e
  }
  await sendFeedback(f)
  assert.equal(f.calls.filter(x => x.name === 'createComment').length, 1)
})

test('no-op success never claims to have produced a commit', () => {
  const body = feedbackBody({mode:'finish', outcome:'success',
    runUrl:'https://github.com/test', changed:'false', commitSha:''})
  assert.match(body,/No changes/)
})

test('unknown commit output is not presented as successful publication', () => {
  const body = feedbackBody({mode:'finish', outcome:'success',
    runUrl:'https://github.com/test', changed:'true', commitSha:''})
  assert.match(body,/could not/)
})

test('rejects malformed workflow run IDs', async () => {
  const f = fixture({RUN_ID:'../bad'})
  await assert.rejects(sendFeedback(f), /Invalid/)
  assert.equal(f.calls.length,0)
})

test('removes only this bot acknowledgement on completion', async () => {
  const f = fixture({STATUS_MODE:'finish', STATUS_COMMENT_ID:'371',
    FEEDBACK_APP_SLUG:'librecode-workflow-automation'})
  f.github.rest.reactions.listForIssueComment = async () => ({data:[
    {id:1, content:'eyes', user:{login:'librecode-workflow-automation[bot]'}},
    {id:2, content:'eyes', user:{login:'another-user'}},
  ]})
  await sendFeedback(f)
  const removed = f.calls.filter(x => x.name === 'remove')
  assert.equal(removed.length, 1)
  assert.equal(removed[0].args.reaction_id, 1)
})
