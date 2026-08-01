const fs = require('fs');
const path = require('path');
const root = path.resolve('.');
const idxPath = path.join(root, 'index.html');
const text = fs.readFileSync(idxPath, 'utf8');
const marker = '<main class="flex-1">';
const start = text.indexOf(marker);
if (start === -1) {
  throw new Error('main start marker not found');
}
const end = text.lastIndexOf('</main>');
if (end === -1) {
  throw new Error('main end marker not found');
}
const header = text.slice(0, start + marker.length);
const body = text.slice(start + marker.length, end);
const footer = text.slice(end);
const incDir = path.join(root, 'include');
fs.mkdirSync(incDir, { recursive: true });
fs.writeFileSync(path.join(incDir, 'header.php'), header, 'utf8');
fs.writeFileSync(path.join(incDir, 'footer.php'), footer, 'utf8');
fs.writeFileSync(path.join(root, 'index.php'), "<?php\ninclude 'include/header.php';\n?>\n" + body + "\n<?php\ninclude 'include/footer.php';\n?>\n", 'utf8');
console.log('created include/header.php, include/footer.php, index.php');
