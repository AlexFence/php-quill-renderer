<?php

namespace DBlackborough\Quill\Tests\Api;

require __DIR__ . '../../../vendor/autoload.php';

use DBlackborough\Quill\Render as QuillRender;

/**
 * Specific tests for raised bugs
 */
final class BugTest extends \PHPUnit\Framework\TestCase
{
    private $delta_bug_101 = '{
  "ops": [
    {
      "insert": "Hallo"
    },
    {
      "attributes": {
        "header": 1
      },
      "insert": "\n"
    },
    {
      "insert": "\nDas ist ein normaler Text:\n\n"
    },
    {
      "attributes": {
        "bold": true
      },
      "insert": "Test: Eintrag"
    },
    {
      "insert": "\n"
    },
    {
      "attributes": {
        "bold": true
      },
      "insert": "Test: Zwei"
    },
    {
      "insert": "\n\nhttps://heartbeat.gmbh\n\n\n"
    }
  ]
}';

    private $delta_bug_108 = '{
    "ops":[
        {"insert":"Wilt u één van bovenstaande punten wijzigen, dan "},
        {"attributes":{"link":"https://studiekeuze123.test/contact"},"insert":"contactformulier"},
        {"insert":".  asd\n\nWijzigingen die "},
        {"attributes":{"color":"#24372e"},"insert":"uiterlijk 20 november 2018 "},
        {"insert":"bij ons zijn binnengekomen, worden nog verwerkt in release 18.10 op 12 december 2018. Wijzigingen in CROHO (zoals nieuwe opleidingen) dienen ook uiterlijk 20 november door u te zijn ingevoerd om meegenomen te worden in de release van december 2018."}
    ]
}';

    private $delta_bug_external_3 = '{"ops":[{"insert":"Lorem ipsum\nLorem ipsum\n\nLorem ipsum\n"}]}';

    private $expected_bug_101 = "<h1>Hallo</h1>
<p>Das ist ein normaler Text:</p>
<p><strong>Test: Eintrag</strong><br />
<strong>Test: Zwei</strong></p>
<p>https://heartbeat.gmbh</p>
<p></p>";

    private $expected_bug_108 = '';

    private $expected_bug_external_3 = "<p>Lorem ipsum<br />
Lorem ipsum</p>
<p>Lorem ipsum</p>";

    /**
     * Inserts with just a new line are being ignored
     * Submitted by https://github.com/nadar -
     * Bug report https://github.com/deanblackborough/php-quill-renderer/issues/101
     *
     * @return void
     * @throws \Exception
     */
    public function testNewlineOnlyInsertsIgnored()
    {
        $result = null;

        try {
            $quill = new QuillRender($this->delta_bug_101);
            $result = $quill->render();
        } catch (\Exception $e) {
            $this->fail(__METHOD__ . 'failure, ' . $e->getMessage());
        }

        $this->assertEquals(
            $this->expected_bug_101,
            trim($result),
            __METHOD__ . ' newline only inserts ignored failure'
        );
    }

    /**
     * Newlines still proving to be an issue
     * Bug report https://github.com/nadar/quill-delta-parser/issues/3
     *
     * @return void
     * @throws \Exception
     */
    public function testNewlinesNotGeneratingNewParagraph()
    {
        $result = null;

        try {
            $quill = new QuillRender($this->delta_bug_external_3);
            $result = $quill->render();
        } catch (\Exception $e) {
            $this->fail(__METHOD__ . 'failure, ' . $e->getMessage());
        }

        $this->assertEquals(
            $this->expected_bug_external_3,
            trim($result),
            __METHOD__ . ' newline issues, no new paragraph'
        );
    }

    /**
     * Issue with links going to a new line
     * Bug report https://github.com/deanblackborough/php-quill-renderer/issues/108
     *
     * @return void
     * @throws \Exception
     */
    public function testLinksOnANewLine()
    {
        $result = null;

        try {
            $quill = new QuillRender($this->delta_bug_108);
            $result = $quill->render();
        } catch (\Exception $e) {
            $this->fail(__METHOD__ . 'failure, ' . $e->getMessage());
        }

        $this->assertEquals(
            $this->expected_bug_108,
            trim($result),
            __METHOD__ . ' links appear on their own line'
        );
    }
}
