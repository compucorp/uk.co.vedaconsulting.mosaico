<?php

/**
 * Class CRM_Mosaico_DefaultComposerScope
 *
 * Suppresses FlexMailer's default composer while a Mosaico mailing composes, then restores it.
 *
 * `civi_flexmailer_default_composer` is a container singleton, so disabling it without restoring
 * leaks into every later compose in the same PHP process (e.g. a cron run delivering several
 * mailings): non-Mosaico mailings would render no body and be silently skipped at send time.
 * Bracketing the compose event keeps the suppression exactly as long as it is needed: both
 * composers (MosaicoComposer at WEIGHT_MAIN, the default composer at WEIGHT_MAIN - 100) run
 * inside WEIGHT_START/WEIGHT_END.
 */
class CRM_Mosaico_DefaultComposerScope {

  /**
   * Default-composer flag values saved by onStartCompose, restored by onEndCompose.
   *
   * @var array
   */
  private $stack = [];

  /**
   * @param \Civi\FlexMailer\Event\ComposeBatchEvent $e
   */
  public function onStartCompose(\Civi\FlexMailer\Event\ComposeBatchEvent $e) {
    if (!$this->isMosaico($e)) {
      return;
    }
    $composer = \Civi::service('civi_flexmailer_default_composer');
    $this->stack[] = $composer->isActive();
    $composer->setActive(FALSE);
  }

  /**
   * @param \Civi\FlexMailer\Event\ComposeBatchEvent $e
   */
  public function onEndCompose(\Civi\FlexMailer\Event\ComposeBatchEvent $e) {
    if (!$this->isMosaico($e) || empty($this->stack)) {
      return;
    }
    \Civi::service('civi_flexmailer_default_composer')->setActive(array_pop($this->stack));
  }

  /**
   * @param \Civi\FlexMailer\Event\ComposeBatchEvent $e
   * @return bool
   */
  private function isMosaico(\Civi\FlexMailer\Event\ComposeBatchEvent $e) {
    return $e->getMailing()->template_type === 'mosaico';
  }

}
