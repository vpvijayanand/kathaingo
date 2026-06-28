# Writing Assistant - Phase 2 Planning

Phase 1 (Stabilization & Core Features) is complete. The following features are planned for Phase 2.

## 1. Learning from Writer Corrections
When the Writing Assistant marks a word as incorrect and the writer replaces it with a suggestion (or manually corrects it), the system should store this correction pair in a dedicated learning table.

**Examples:**
- `என்னது` → `என்னுடைய`
- `வால்தியா` → `வாழ்த்திய`
- `விடு` → `வீடு`

**Requirements:**
- These pairs MUST NOT automatically enter the master dictionary.
- They should be stored as `correction-pattern` data.
- The system should rank these patterns by:
  1. Frequency of occurrence
  2. Writer trust level
  3. Administrator/community approval status

## 2. Fix All Similar Mistakes (Bulk Correction)
When the same mistaken word appears multiple times in a single article/editor session, the writer should be able to apply the correction globally.

**Requirements:**
- Allow the writer to choose "Fix this occurrence" or "Fix all occurrences in this article".
- This feature MUST be completely optional.
- Global replacement MUST carefully preserve existing formatting, spacing, and punctuation around the corrected words.
- Global replacement should trigger the same TinyMCE safe-replacement logic as single replacements to avoid DOM corruption.
