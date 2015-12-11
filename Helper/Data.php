<?php

namespace Ihb\ModuleCreator\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 * @package Ihb\ModuleCreator\Helper
 * @author Dmitriy Antonenko <indaheartbeat@gmail.com>
 */
class Data extends AbstractHelper
{
    /**
     * @param $checkWord
     * @return bool
     */
    public function checkForReservedWords($checkWord)
    {
        $keywords = array('__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor');
        $predefinedConstants = array('__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__', '__LINE__', '__METHOD__', '__NAMESPACE__', '__TRAIT__');

        if (in_array ($checkWord, $keywords) || in_array ($checkWord, $predefinedConstants)) {
            return true;
        }

        return false;
    }
}
