/**
 * Updates the OPENLAB_VERSION constant in functions.php.
 * Replaces grunt setPHPConstant. Run via `npm run set-version`.
 */

const fs   = require( 'fs' );
const path = require( 'path' );

const functionsFile = path.join( __dirname, '..', 'functions.php' );
const { version }   = require( '../package.json' );

const timestamp   = Date.now();
const fullVersion = `${ version }-${ timestamp }`;

let content = fs.readFileSync( functionsFile, 'utf8' );

content = content.replace(
	/define\( 'OPENLAB_VERSION', '[^']*' \);/,
	`define( 'OPENLAB_VERSION', '${ fullVersion }' );`
);

fs.writeFileSync( functionsFile, content );
console.log( `Version set to ${ fullVersion }` );
