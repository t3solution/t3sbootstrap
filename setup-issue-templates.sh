#!/usr/bin/env bash
set -e

# Sicherheits-Check: sind wir in einem Git-Repo?
if [ ! -d ".git" ]; then
  echo "❌ Bitte im Wurzelverzeichnis des Repos ausführen (wo .git/ liegt)."
  exit 1
fi

mkdir -p .github/ISSUE_TEMPLATE

# --- config.yml -------------------------------------------------------------
cat > .github/ISSUE_TEMPLATE/config.yml <<'EOF'
blank_issues_enabled: false
contact_links:
  - name: 💬 TYPO3 Slack
	url: https://typo3.slack.com
	about: Please post general questions in TYPO3 Slack.
EOF

# --- bug.yml ----------------------------------------------------------------
cat > .github/ISSUE_TEMPLATE/bug.yml <<'EOF'
name: 🐛 Bug Report
description: Something isn't working as expected
title: "[Bug]: "
labels: ["🐛 bug"]
body:
  - type: input
	id: typo3-version
	attributes:
	  label: TYPO3-Version
	  placeholder: e.g. 14.3
	validations:
	  required: true
  - type: input
	id: extension-version
	attributes:
	  label: t3sbootstrap-Version
	  placeholder: e.g. 5.3.43
	validations:
	  required: true
  - type: textarea
	id: description
	attributes:
	  label: What's going on?
	  description: Describe the problem and how to reproduce it.
	validations:
	  required: true
  - type: textarea
	id: expected
	attributes:
	  label: Expected behavior
	validations:
	  required: true
EOF

# --- feature.yml ------------------------------------------------------------
cat > .github/ISSUE_TEMPLATE/feature.yml <<'EOF'
name: ✨ Feature Request
description: Suggestion for a new feature
title: "[Feature]: "
labels: ["✨ feature"]
body:
  - type: textarea
	id: problem
	attributes:
	  label: What problem does this feature solve?
	validations:
	  required: true
  - type: textarea
	id: solution
	attributes:
	  label: Suggested solution
	validations:
	  required: true
EOF

# --- question.yml -----------------------------------------------------------
cat > .github/ISSUE_TEMPLATE/question.yml <<'EOF'
name: ❓ Frage
description: Question about using t3sbootstrap
title: "[Question]: "
labels: ["❓ question"]
body:
  - type: textarea
	id: question
	attributes:
	  label: Your question
	validations:
	  required: true
EOF

echo "✅ Templates created under .github/ISSUE_TEMPLATE/"
ls -la .github/ISSUE_TEMPLATE/

echo ""
echo "Next Steps:"
echo "  git add .github/ISSUE_TEMPLATE/"
echo "  git commit -m 'Add issue templates'"
echo "  git push"
