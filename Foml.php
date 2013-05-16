<?php

/*
 * If you are NOT using a class autoloader 
 * you should require_once 'FomlConfig.php'
 * to pre-load all of the Foml classes.
 */

class Foml
{
    const PHP_MODE = 'php';
    const XML_MODE = 'xml';

    static $fopExec = "fop-1.1/fop";           // fopExec is relative to this directory
    static $tempDir = null;                    // defaults to system temp directory
    static $keepTempFiles = true;              // set to true for debugging
    static $pdfMimeType = "application/pdf";
    static $defaultNamespace = "fo";

    // PHP 5.4 and beyond supports XML via htmlentities, but for now we will escape manually. 
    // courtesy of 
    // http://stackoverflow.com/questions/3957360/generating-xml-document-in-php-escape-characters
    static function XmlEntities($string) 
    {
        return str_replace(array("&", "<", ">", "\"", "'"),
                           array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;"),
                           $string);
    }

    static function GeneratePhp($Template)
    {
        $fomlParser = new FomlParser();
        $php = $fomlParser->ParseFile($Template);
        return $php;
    }

    // returns XSL-FO string
    // REVISIT - use ob_start with a callback function to capture output directly to a file,
    // and return a temporary filename instead of a string.
    static function GenerateXslFo($Template, $Args=null)
    {
        ob_start();
        Foml::RenderFoml($Template, $Args);
        $xslFo = ob_get_contents();
        ob_end_clean();
        return $xslFo;
    }

    static function RenderFoml($Template, $Args)
    {
        // import variables
        if ($Args) {
            foreach ($Args as $key=>$value) {
                $$key = $value;
            }
        }
        $_php = Foml::GeneratePhp($Template);
        //Dump(htmlspecialchars($_php)); return;
        eval("?".">".$_php);  // prefixed with ? > to exit implicit php mode
    }

    static function TempName($Prefix)
    {
        $tempDir = Foml::$tempDir;
        if ($tempDir == null) $tempDir = sys_get_temp_dir();
        return tempnam($tempDir, $Prefix);
    }

    // returns the name of the temporary pdf output file.
    // The file must be unlinked by the caller.
    static function XslFoToPdf($XslFo)
    {
        $fomlDir = dirname(__FILE__);
        $docRoot = GetIfSet($_SERVER, 'DOCUMENT_ROOT');
        $confFileName = "{$fomlDir}/fop.xconf";
        $pdfFileName = Foml::TempName("pdf-");
        $xslFoFileName = Foml::TempName("xslfo-");

        $xslFile = fopen($xslFoFileName, "w");
        fwrite($xslFile, $XslFo);
        fclose($xslFile);

        $escapedPdfFileName = escapeshellarg($pdfFileName);
        $escapedXslFoFileName = escapeshellarg($xslFoFileName);
        $escapedConfFileName = escapeshellarg($confFileName);
        $fop = $fomlDir.'/'.Foml::$fopExec;

        // We set the cwd to the directory this file is in for the subprocess.
        // This allows us to use relative a path in fop.xconf to the fonts directory
        // so we don't have to edit the conf file depending on our installation path.
        // REVISITED:
        // I was not able to get baseUrl or baseDir to work with relative paths for external-graphic nodes.
        // So instead we have changed the cwd to docRoot, and set a fully-qualified path for the configuration file.
        $cwd = $docRoot;

        $cmd = "{$fop} {$escapedXslFoFileName} {$escapedPdfFileName} -c {$escapedConfFileName}";
        $env = $_ENV;

        // It seems that something changed (at FreeBSD 9.1?) that means that apache no longer
        // has /usr/local/bin in the path by default.  We add it here for fop because that is where
        // Diablo Java(TM) SE Runtime Environment (build 1.6.0_07-b02)
        // is installed.
        $env['PATH'].= ':/usr/local/bin';

        // Fop tries to create a font cache at [user.home]/.fop/fop-fonts.cache
        // see: https://github.com/apache/fop/blob/trunk/src/java/org/apache/fop/fonts/FontCache.java
        // Because the apache-user doesn't have a home directory, this causes a fatal error unless we
        // specify a new user.home value in FOP_OPTS.  Note that for the cache to work
        // you should create FOML/.fop and make it writable by the webserver user.
        $env['FOP_OPTS'] = "";
        $env['FOP_OPTS'].= "-Duser.home={$fomlDir}";
        $env['FOP_OPTS'].= " -Xmx1024m";  // give the JVM extra memory - needed on small systems for complex documents

        $outFile = Foml::TempName("stdout-");
        $errFile = Foml::TempName("stderr-");
        $descriptors = array(0=>array("pipe", "r"),
                             1=>array("file", $outFile, "w"),
                             2=>array("file", $errFile, "w")
                             );

        // to run from the command line use something like this:
        //   setenv FOP_OPTS '-Duser.home=/usr/data/www/FOML -Xmx1024m'
        //   /usr/data/www/FOML/fop-1.0/fop '/var/tmp/xslfo-tsy5I1' '/var/tmp/pdf-iqe6y5' -c '/usr/data/www/FOML/fop.xconf'
        //
        // to run TTFReader  
        //    Note that metrics files are optional and no longer required
        //    java -cp "lib/FOML/fop-1.1/build/fop.jar:lib/FOML/fop-1.1/lib/avalon-framework-4.2.0.jar:lib/FOML/fop-1.1/lib/commons-io-1.3.1.jar:lib/FOML/fop-1.1/lib/commons-logging-1.0.4.jar:lib/FOML/fop-1.1/lib/xmlgraphics-commons-1.4.jar" org.apache.fop.fonts.apps.TTFReader  input.ttf output.xml
        //

        //print_r(array('cmd'=>$cmd,
        //'cwd'=>$cwd,
        //'env'=>$env)); exit;
        
        $proc = proc_open($cmd, $descriptors, $pipes, $cwd, $env);
        if (!is_resource($proc)) {
            // TODO - raise exception here?
            print "Failure opening process"; exit;
        }

        fclose($pipes[0]);  // close stdin without sending anything

        $exit = proc_close($proc);
        if ($exit!=0) {
            $stdout = file($outFile);
            $stderr = file($errFile);
            unlink($outFile);
            unlink($errFile);
            Dump("cmd={$cmd}");
            Dump("result={$exit}");
            Dump(join($stdout,""));
            Dump(join($stderr,""));
            Dump(htmlspecialchars($XslFo));
            exit;
        }
        
        unlink($outFile);
        unlink($errFile);

        if (!Foml::$keepTempFiles) unlink($xslFoFileName);

        return $pdfFileName;
    }

    static function RenderToFile($Template, $Args)
    {
        $xslFo = Foml::GenerateXslFo($Template, $Args);
        $pdfFileName = Foml::XslFoToPdf($xslFo);
        return $pdfFileName;
    }

    static function Render($Template, $Args=null, $Headers=null)
    {
        $pdfFileName = self::RenderToFile($Template, $Args);
        $size = filesize($pdfFileName);
        $pdfMimeType = Foml::$pdfMimeType;

        if ($Headers) {
            foreach ($Headers as $header) {
                header($header);
            }
        }
        header("Content-Length: {$size}");
        header("Content-Type: {$pdfMimeType}");

        $fileHandle = fopen($pdfFileName, "rb");
        fpassthru($fileHandle);
        fclose($fileHandle);

        if (!Foml::$keepTempFiles) unlink($pdfFileName);
    }
    static function RenderInline($Template, $Args=null)
    {
        $headers = array("Content-Disposition"=>"inline");
        Foml::Render($Template, $Args, $headers);
    }

    static function RenderAttachment($Template, $Filename, $Args=null)
    {
        $headers = array("Content-Disposition: attachment; filename=\"{$Filename}\"");
        Foml::Render($Template, $Args, $headers);
    }

    static function FormatText($Text)
    {
        $xlsFo = "";
        foreach (preg_split('/(\r\n)|(\n\r)|\n|\r/', $Text) as $line) {
            $xlsFo.= "<fo:block>".self::XmlEntities($line)."</fo:block>";
        }
        return $xlsFo;
        
    }
}

?>
