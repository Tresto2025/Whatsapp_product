# Working rules

## Autonomy
- Act, don't ask. Only ask when a choice is irreversible AND ambiguous
  (deleting data, force-push, prod DB migrations, changing billing/infra).
- Make reasonable assumptions, state them in one line, and continue.
- No "should I proceed?" checks. Finish the full task: code → test → commit → push → deploy.

## Workflow
1. Install deps if needed: `npm ci`
2. Make changes
3. Run `npm run lint && npm run build && npm test`. Fix failures yourself.
4. Commit with a clear message, then push.
5. Deploy: `vercel --prod --token $VERCEL_TOKEN --yes`
6. Report back: what changed, the deploy URL, and anything skipped.

## Hard limits (never do)
- No `git push --force`, `rm -rf` outside the repo, or rewriting history on main
- Never print, commit, or log secrets or `.env` values
- No new paid services or dependencies over ~1MB without mentioning them

## Project notes
- Stack: <fill in>
- Deploy target: <Vercel / other>
