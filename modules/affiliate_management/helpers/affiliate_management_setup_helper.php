<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Generates a regular expression pattern to match the signature for requiring a file.
 *
 * The signature pattern is in the format:
 *     #//affiliate-management:start:<filename>([\s\S]*)//affiliate-management:end:<filename>#
 * where <filename> is the basename of the file.
 *
 * @param string $file The path to the file.
 *
 * @return string The regular expression pattern for the file signature.
 */
function affiliate_management_require_signature($file)
{
    $basename = str_ireplace(['"', "'"], '', basename($file));
    return '#//affiliate-management:start:' . $basename . '([\s\S]*)//affiliate-management:end:' . $basename . '#';
}

/**
 * Generates the template for requiring a file in Perfex SAAS.
 *
 * This function generates the template for requiring a file in Perfex SAAS. The template includes comments that mark
 * the start and end of the required file. The template is in the following format:
 *     //affiliate-management:start:#filename
 *     //dont remove/change above line
 *     require_once('#path');
 *     //dont remove/change below line
 *     //affiliate-management:end:#filename
 * where #filename is replaced with the basename of the file, and #path is replaced with the actual path to the file.
 *
 * @param string $path The path to the file.
 *
 * @return string The template for requiring the file.
 */
function affiliate_management_require_in_file_template($path)
{
    $template = "//affiliate-management:start:#filename\n//dont remove/change above line\nrequire_once(#path);\n//dont remove/change below line\n//affiliate-management:end:#filename";

    $template = str_ireplace('#filename', str_ireplace(['"', "'"], '', basename($path)), $template);
    $template = str_ireplace('#path', $path, $template);
    return $template;
}

/**
 * Writes content to a file.
 *
 * It sets the appropriate file permissions, opens the file,
 * writes the content, and closes the file.
 *
 * @param string $path    The path to the file.
 * @param string $content The content to write to the file.
 *
 * @return bool True if the write operation was successful, false otherwise.
 */
function affiliate_management_file_put_contents($path, $content)
{
    @chmod($path, FILE_WRITE_MODE);
    if (!$fp = fopen($path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
        return false;
    }
    flock($fp, LOCK_EX);
    fwrite($fp, $content, strlen($content));
    flock($fp, LOCK_UN);
    fclose($fp);
    @chmod($path, FILE_READ_MODE);
    return true;
}

/**
 * Requires a file into another file.
 *
 * The function uses a template to generate the require statement and inserts it at the specified
 * position in the destination file. If no position is specified, the require statement is appended to the end of the
 * file.
 *
 * @param string  $dest        The path to the destination file.
 * @param string  $requirePath The path to the file to require.
 * @param bool    $force       Whether to force the insertion even if it already exists.
 * @param int|bool $position    The position to insert the require statement. False to append to the end of the file.
 *
 * @return void
 */
function affiliate_management_require_in_file($dest, $requirePath, $force = false, $position = false)
{
    if (!file_exists($dest)) {
        affiliate_management_file_put_contents($dest, "<?php defined('BASEPATH') or exit('No direct script access allowed');\n");
    }

    if (file_exists($dest)) {
        $content = file_get_contents($dest);  // Fetch the content inside the file

        $template = affiliate_management_require_in_file_template($requirePath);

        $exist = preg_match(affiliate_management_require_signature($requirePath), $content);
        if ($exist && !$force) { // Check if this process has run once or not
            return;
        }
        $content = affiliate_management_unrequire_in_file($dest, $requirePath);

        if ($position !== false) {
            $content = substr_replace($content, $template . "\n", $position, 0);
        } else {
            $content = $content . $template;
        }

        affiliate_management_file_put_contents($dest, $content);
    }
}

/**
 * Removes the require statement of a file.
 *
 * This function removes the require statement from a file in Perfex SAAS.
 * It fetches the content inside the destination file, replaces the require statement with an
 * empty string using a regular expression, and then updates the destination file with the modified content.
 *
 * @param string $dest        The path to the destination file.
 * @param string $requirePath The path to the file to be removed from the require statement.
 *
 * @return string The modified content of the destination file.
 */
function affiliate_management_unrequire_in_file($dest, $requirePath)
{
    if (file_exists($dest)) {
        $content = file_get_contents($dest);  // Fetch the content inside the file
        $content = preg_replace(affiliate_management_require_signature($requirePath), '', $content);
        affiliate_management_file_put_contents($dest, $content);
        return $content;
    }
}

/**
 * Installs Page builder
 *
 * @return void
 */
function affiliate_management_install()
{
    // Require the SAAS routes and hooks
    affiliate_management_require_in_file(APPPATH . 'config/my_routes.php', "FCPATH.'modules/" . AFFILIATE_MANAGEMENT_MODULE_NAME . "/config/my_routes.php'");

    $CI = &get_instance();
    require_once(module_dir_path(AFFILIATE_MANAGEMENT_MODULE_NAME, 'install.php'));
}


/**
 * Uninstalls Page builder
 * @param bool $clean (Optional) Determines whether to perform a clean uninstall by removing all data. Defaults to false.
 * @return void
 */
function affiliate_management_uninstall()
{
    // Remove the SAAS routes and hooks
    affiliate_management_unrequire_in_file(APPPATH . 'config/my_routes.php', "FCPATH.'modules/" . AFFILIATE_MANAGEMENT_MODULE_NAME . "/config/my_routes.php'");
}
