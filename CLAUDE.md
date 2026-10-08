# CLAUDE.md

Read and follow [AGENTS.md](AGENTS.md); it is the single source of instructions for AI agents in this
repository.

@AGENTS.md

## Claude Code specifics

- Repository skill: `.claude/skills/regenerate-smart-api` (refreshing the generated Smart endpoints).
- Consumer skill shipped to applications: `resources/boost/skills/smart-pension-api/SKILL.md`.
- Generated files (`src/SMART/Api/Resources/*` except `AbstractResource.php`, `docs/smart/endpoints.md`,
  `docs/smart/resources/*`) are rebuilt by `bin/generate-smart-api.php`; do not edit them directly.
