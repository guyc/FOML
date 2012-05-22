<?php

require_once 'FomlConfig.php';

class FOML
{
    static $fopExec = "fop-1.0/fop";           // fopExec is relative to this directory
    static $pdfMimeType = "application/pdf";
    static $tempDir = null;                    // defaults to system temp directory
    static $keepTempFiles = true;             // set to true for debugging

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
        // import variables
        if ($Args) {
            foreach ($Args as $key=>$value) {
                $$key = $value;
            }
        }
        $_php = FOML::GeneratePhp($Template);

        ob_start();
        eval("?".">".$_php);  // prefixed with ? > to exit implicit php mode
        $xslFo = ob_get_contents();
        ob_end_clean();
        return $xslFo;
    }

    static function TempName($Prefix)
    {
        $tempDir = FOML::$tempDir;
        if ($tempDir == null) $tempDir = sys_get_temp_dir();
        return tempnam($tempDir, $Prefix);
    }

    // returns the name of the temporary pdf output file.
    // The file must be unlinked by the caller.
    static function XslFoToPdf($XslFo)
    {
        $pdfFileName = FOML::TempName("pdf-");
        $xslFoFileName = FOML::TempName("xslfo-");

        $xslFile = fopen($xslFoFileName, "w");
        fwrite($xslFile, $XslFo);
        fclose($xslFile);

        $escapedPdfFileName = escapeshellarg($pdfFileName);
        $escapedXslFoFileName = escapeshellarg($xslFoFileName);
        $fop = dirname(__FILE__).'/'.FOML::$fopExec;
        $cmd = "{$fop} {$escapedXslFoFileName} {$escapedPdfFileName}";
        shell_exec($cmd);

        if (!FOML::$keepTempFiles) unlink($xslFoFileName);

        return $pdfFileName;
    }

    static function Render($Template, $Args=null, $Headers=null)
    {
        $xslFo = FOML::GenerateXslFo($Template, $Args);
        $pdfFileName = FOML::XslFoToPdf($xslFo);
        $size = filesize($pdfFileName);
        $mimeType = FOML::$pdfMimeType;

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

        if (!FOML::$keepTempFiles) unlink($pdfFileName);
    }
    static function RenderInline($Template, $Args=null)
    {
        $headers = array("Content-Disposition"=>"inline");
        FOML::Render($Template, $Args, $headers);
    }

    static function RenderAttachment($Template, $Filename, $Args=null)
    {
        $headers = array("Content-Disposition: attachment; filename=\"{$FileName}\"");
        FOML::Render($Template, $Args, $headers);
    }
}

?>
