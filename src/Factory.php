<?php


namespace EasySwoole\WeChat;


use EasySwoole\WeChat\OfficialAccount\Application as OfficialAccount;
use EasySwoole\WeChat\Work\Application as Work;

class Factory
{
    /**
     * @param mixed ...$arguments
     * @return OfficialAccount
     */
    public static function officialAccount(...$arguments)
    {
        return new OfficialAccount(...$arguments);
    }

    /**
     * @param mixed ...$arguments
     * @return Work
     */
    public static function work(...$arguments)
    {
        return new Work(...$arguments);
    }
}