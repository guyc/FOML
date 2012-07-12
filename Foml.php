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

    static $fopExec = "fop-1.0/fop";           // fopExec is relative to this directory
    static $tempDir = null;                    // defaults to system temp directory
    static $keepTempFiles = true;              // set to true for debugging
    static $pdfMimeType = "application/pdf";
    static $defaultNamespace = "fo";

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
        $confFileName = "fop.xconf";
        $pdfFileName = Foml::TempName("pdf-");
        $xslFoFileName = Foml::TempName("xslfo-");

        $xslFile = fopen($xslFoFileName, "w");
        fwrite($xslFile, $XslFo);
        fclose($xslFile);

        $escapedPdfFileName = escapeshellarg($pdfFileName);
        $escapedXslFoFileName = escapeshellarg($xslFoFileName);
        $escapedConfFileName = escapeshellarg($confFileName);
        $fop = Foml::$fopExec;

        // We set the cwd to the directory this file is in for the subprocess.
        // This allows us to use relative a path in fop.xconf to the fonts directory
        // so we don't have to edit the conf file depending on our installation path.
        $cwd = $fomlDir;

        $cmd = "{$fop} {$escapedXslFoFileName} {$escapedPdfFileName} -c {$escapedConfFileName}";
        $env = $_ENV;
        // Fop tries to create a font cache at [user.home]/.fop/fop-fonts.cache
        // see: https://github.com/apache/fop/blob/trunk/src/java/org/apache/fop/fonts/FontCache.java
        // Because the apache-user doesn't have a home directory, this causes a fatal error unless we
        // specify a new user.home value in FOP_OPTS.  Note that for the cache to work
        // you should create FOML/.fop and make it writable by the webserver user.
        $env['FOP_OPTS'] = "-Duser.home={$fomlDir}";

        $outFile = Foml::TempName("stdout-");
        $errFile = Foml::TempName("stderr-");
        $descriptors = array(0=>array("pipe", "r"),
                             1=>array("file", $outFile, "w"),
                             2=>array("file", $errFile, "w")
                             );

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

    static function Render($Template, $Args=null, $Headers=null)
    {
        $xslFo = Foml::GenerateXslFo($Template, $Args);
        $pdfFileName = Foml::XslFoToPdf($xslFo);
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
}

?>
