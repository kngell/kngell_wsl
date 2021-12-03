<?php

declare(strict_types=1);

trait SystemTrait
{
    /**
     * Init Session
     * =====================================================================.
     * @param bool $useSessionGlobals
     * @return void
     */
    public static function sessionInit(bool $useSessionGlobals = false)
    {
        $session = SessionManager::initialize();
        if (!$session) {
            throw new BaseLogicException('Please enable session within session.yaml configuration');
        }
        if ($useSessionGlobals == true) {
            GlobalsManager::set('global_session', $session);
        } else {
            return $session;
        }
    }
}
