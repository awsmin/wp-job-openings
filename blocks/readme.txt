WP Job Openings - Block Notes
=============================

- Block: `wp-job-openings/blocks`
- Dynamic block with server-side rendering.
- Filter placement supports:
  - `top`
  - `side`
- Search and filters support URL deep-linking.
- Layout supports list, grid, and stack.
- Responsive filter toggle is supported.
- Keep translations with `__( 'Text', 'wp-job-openings' )`.

Main files
----------
- `blocks/src/block.json`
- `blocks/src/inspector.js`
- `blocks/src/edit.js`
- `blocks/src/view.js`
- `inc/templates/block-files/block-job-openings-view.php`

Build
-----
- `cd blocks && npm install`
- `npm run build`
