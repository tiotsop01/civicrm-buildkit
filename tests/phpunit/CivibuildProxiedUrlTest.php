<?php

use \Civi\Civibuild\ProcessUtil;

class CivibuildProxiedUrlTest extends \Civi\Civibuild\CivibuildTestCase {


/**
   * @var string
   * Create a build with the given alias (e.g. `http://proxied-url.example.org`) .
   */
  protected $proxied_url = CIVICRM_UF_BASEURL;


protected function setUp() {
    parent::setUp();
    $fs = new \Symfony\Component\Filesystem\Filesystem();
    $fs->remove($this->getAbsPath($this->proxied_url));
    $fs->remove($this->getAbsPath($this->proxied_url . '.sh'));
    ProcessUtil::runOk($this->cmd('amp cleanup'));
  }

public function getCases() {
    $cases = array();

    $cases[] = array(
      "civibuild create {$this->buildName} --type {$this->buildType} --url CIVICRM_UF_BASEURL='http://proxied-url.example.org'",
      CIVICRM_UF_BASEURL="http://proxied-url.example.org",
    );

    return $cases;
  }
  /**
   * @param string $command
   * @param string $expectedUrl
   * @dataProvider getCases
   */

  public function testBuildUrl($command, $expectedUrl) {
    $result = ProcessUtil::runOk($this->cmd($command));
    $this->assertRegExp(";Execute [^\n]*/download.sh;", $result->getOutput());
    $this->assertRegExp(";Execute [^\n]*/install.sh;", $result->getOutput());
    $this->assertContains("- CMS_URL: $expectedUrl\n", $result->getOutput());
  }

}

