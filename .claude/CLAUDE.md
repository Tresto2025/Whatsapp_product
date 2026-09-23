# Working rules

The user hands over a job and expects working, tested software back. They do not test basic things (login, CRUD, forms, navigation) for you. If they find a bug that a test could have caught, that is a failure of this process.

## Autonomy
- Discovery first: read the code, the README and package.json, and run the existing tests to get a baseline. That needs no permission.
- Then build, test and fix on your own. Do not stop to ask "should I proceed?" or to have the user try something.
- Ask only when a choice is irreversible AND ambiguous: deleting real data, prod DB migrations, force-push, paid services, infra changes. Otherwise make the reasonable assumption, note it in one line, and continue.
- Never ask the user for screenshots or console output you can get yourself. Run the app, use Playwright, and read the logs and screenshots.

## Definition of done
A task is done only when all of these are true, and you have seen each one pass in this session:
1. `npm run lint`, typecheck, unit tests and e2e tests all pass, and you ran them yourself after your last change. Fix the cause of every failure. Never skip, delete or loosen a test to get green.
2. Every user-facing change has a Playwright e2e test that exercises it through the UI against a real (local or test) database:
   - **Auth:** login succeeds with valid credentials, fails with wrong ones, logout works, and protected pages redirect when logged out.
   - **CRUD:** create, read it back (list + detail, after reload), update and see the change, delete or archive and see it gone.
   - **Validation and permissions:** bad input is rejected with a readable message, and unauthorised roles are refused.
3. You started the app and looked at the changed screens yourself, taking screenshots with Playwright and reading them. No broken layout, console errors or failed requests.
4. You re-tested as a separate pass, like a QA engineer trying to break it: edge cases, empty and invalid input, wrong roles, reload after save. Anything that failed is fixed and re-checked.

If a check cannot run (a missing DB, service or credential), try to make it runnable locally first: docker compose, a seed, a test env file. Only if that is impossible, say exactly what is missing. Do not claim the check passed.

## Test environment
- Tests run against a local or test database, never production. Seed the data they need in e2e setup, so every test starts from a known state.
- Keep test credentials in `e2e/support/` or `.env.test`, never in real `.env` files.

## Loop for every job
1. Discover and restate the job in 2–3 lines, including your assumptions.
2. Write or extend the failing e2e test for the behaviour first, when practical.
3. Implement.
4. Run the checks, fix, and repeat until green.
5. Do the QA pass from step 4 of the definition of done, fix what it finds, and re-run everything.
6. Commit on a feature branch with a clear message. Pushing, deploying and merging to main need the user's OK.

## Report back (keep it short)
- What now works, in user terms.
- What was verified: test names or counts, plus which screens you looked at.
- Anything not done or not verifiable, and why.
- Nothing for the user to "try out", unless it truly needs a human (real email inbox, payment, a device).

## Hard limits
- No `git push --force`, no rewriting history on main, no `rm -rf` outside the repo.
- Never print, commit or log secrets or `.env` values.
- No new paid services, or dependencies over ~1MB, without mentioning them.

## Project notes
- Stack: <fill in>
- Start app: <e.g. docker compose up -d && npm run dev>
- Test DB: <how e2e gets its database>
- Deploy: <how, and who approves>
