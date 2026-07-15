/**
 * Compiles LESS color-scheme variants for the theme and toolbar.
 * Replaces grunt-contrib-less. Run via `npm run build-css`.
 */

const less = require( 'less' );
const fs   = require( 'fs' );
const path = require( 'path' );

const themeDir    = path.join( __dirname, '..' );
const colorSchemes = require( './color-schemes.json' );

async function compile( sourceFile, outputFile, modifyVars ) {
	const content = fs.readFileSync( sourceFile, 'utf8' );

	const result = await less.render( content, {
		filename: sourceFile,
		modifyVars,
		optimization: 2,
	} );

	fs.mkdirSync( path.dirname( outputFile ), { recursive: true } );
	fs.writeFileSync( outputFile, result.css );
	console.log( `Built: ${ path.relative( themeDir, outputFile ) }` );
}

async function buildAll() {
	const styleLess   = path.join( themeDir, 'style.less' );
	const toolbarLess = path.join( themeDir, 'less', 'openlab-toolbar.less' );
	const colorDir    = path.join( themeDir, 'css', 'color-schemes' );

	const tasks = [];

	for ( const [ color, vars ] of Object.entries( colorSchemes ) ) {
		tasks.push( compile( styleLess,   path.join( colorDir, `${ color }.css` ),         vars ) );
		tasks.push( compile( toolbarLess, path.join( colorDir, `toolbar-${ color }.css` ), vars ) );
	}

	await Promise.all( tasks );
}

buildAll().catch( err => {
	console.error( err.message || err );
	process.exit( 1 );
} );
