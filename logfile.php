<?php
class logfile{

	function write($the_string) {
		
		if( $fh = @fopen("logfile.txt", "a+") ) {
			fputs( $fh, $the_string, strlen($the_string) );
			fclose( $fh );
			return( true );
		} else {
			return( false );
		}
	}
}
?>