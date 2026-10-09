# TravelOrio translations

English is the default and lives in the HTML. Bangla is layered on top by `core.js`.

| File | What it holds |
|------|---------------|
| `bn.js` | Bangla for every UI string. **Key = the exact English text**, value = Bangla. |
| `data-bn.js` | Bangla for the 6 destinations (`places`) and the 12 sample reviews (`reviews`, same order as `js/data.js`). |
| `core.js` | The engine: `TO.t()`, `TO.money()`, language switch, saved choice, `?lang=bn`. |

## Change or add a translation
1. Find the English sentence in `bn.js` and edit the Bangla value. Missing keys simply show English.
2. If you change the English text in a page, change the key in `bn.js` to match.
3. Strings with variables use `{name}` placeholders, for example `"Day {n}"`.
4. See what is still untranslated: open any page with `?lang=bn&i18n-debug` and read the console warning.

## Helpful attributes
- `data-i18n-html` on an element whose text contains tags (`<em>`): the whole inner HTML is the key.
- `data-no-i18n` on an element that must never be translated.
- `data-count` numbers are formatted by `main.js` in the active language.

## Add a third language
1. Create `js/i18n/xx.js` (copy `bn.js`, set `window.TRAVELORIO_I18N.xx`) and `data-xx.js`.
2. Add `"xx"` to `SUPPORTED` in `core.js`, a button in each page's `.lang-switch`, and load the files.
3. Add a font for that script to the Google Fonts link and a `html[lang="xx"]` block in `css/style.css`.
