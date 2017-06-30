<?php
AddEventHandler("main", "OnEndBufferContent", "pageSpeedReplace");
function pageSpeedReplace(&$content)
{
    global $USER;
    if (is_object($USER) && $USER->IsAdmin())
        return;

    $arPatternsToRemove = Array
    (
        '/<link.+?href=".+?kernel_main\/kernel_main\.css\?\d+"[^>]+>/',
        '/<link.+?href=".+?main\/popup\.css\?\d+"[^>]+>/',
        '/<script.+?>BX\.(setCSSList|setJSList)\(\[.+?\]\).*?<\/script>/',
    );


    $content = preg_replace($arPatternsToRemove, "", $content);
    $content = preg_replace("/\n{2,}/", "\n", $content);

    $cssPattern = '/<link.+?href="(.+?)template_(.+?)\/template_(.+?)\.css\?\d+"[^>]+>/';
    if(preg_match($cssPattern, $content, $matches))
    {
        $filename = $_SERVER["DOCUMENT_ROOT"].$matches[1]."template_".$matches[2]."/template_".$matches[3].".css";
        if(file_exists($filename))
        {
            $styleContent = file_get_contents($filename);
            if($styleContent)
            {
                $content = str_replace($matches[0], '<style>'.$styleContent.'</style>', $content);
            }
        }
    }

    $jsPattern = '/<script.+?src="(.+?)template_(.+?)\/template_(.+?)\.js\?\d+"><\/script\>/';
    if(preg_match($jsPattern, $content, $matches))
    {
        $filename = $_SERVER["DOCUMENT_ROOT"].$matches[1]."template_".$matches[2]."/template_".$matches[3].".js";
        if(file_exists($filename))
        {
            $jsContent = file_get_contents($filename);
            if($jsContent)
            {
                $content = str_replace($matches[0], '<script type="text/javascript">'.$jsContent.'</script>', $content);
            }
        }
    }

}