<?php
/**
 * 测试 Logtube\Output\FileOutput
 *
 * @author MengShaoying
 */
use Logtube\Output\FileOutput;
use Logtube\Event;
use PHPUnit\Framework\TestCase;

class FileOutputTest extends TestCase
{
    /**
     * 测试创建目录能力。
     *
     * @return void
     */
    public function testCreateDir()
    {
        $tempDir = 'logs/' . uniqid() . '/' . uniqid();
        FileOutput::createDirIfNotExisted($tempDir);
        $this->assertTrue(is_dir($tempDir));
        rmdir($tempDir);
    }

    /**
     * 测试添加 event
     *
     * @return void
     */
    public function testAppend()
    {
        $today = date('Y-m-d');
        $subdir = uniqid();
        $env = 'local_' . uniqid();
        $topic = 'topic_' . uniqid();
        $project = 'project_' . uniqid();
        $crid = 'crid_' . uniqid();
        $crsrc = 'crsrc_' . uniqid();
        $keywords = [uniqid(), uniqid(), uniqid()];
        $extras = [
            'ex-key-' . uniqid() => uniqid(),
        ];
        $message = 'Message=' . uniqid();

        mt_srand(0); // 种子设置为0后mt_rand返回的第一个值是5
        $logFile = 'logs/'.$subdir.'/'.$env.'.'.$topic.'.'.$project.'.'.$today.'.5.log';

        $fOut = new FileOutput([
            'dir' => 'logs',
            'subdirs' => [
                $topic => $subdir,
            ],
        ]);
        $event = new Event();
        $event->setEnv($env);
        $event->setTopic($topic);
        $event->setProject($project);
        $event->setCrid($crid);
        $event->setCrsrc($crsrc);
        foreach ($keywords as $keyword) {
            $event->addKeyword($keyword);
        }
        foreach ($extras as $name => $value) {
            $event->addExtra($name, $value);
        }
        $event->setMessage($message);
        $fOut->append($event);
        $this->assertTrue(is_file($logFile));

        $info = $this->parseLogFile($logFile);
        $this->assertTrue(!empty($info));

        $this->assertEquals($today, $info['date']);
        $this->assertEquals($message, $info['message']);

        $this->assertEquals($crid, $info['json'][0]['c']);
        $this->assertEquals($crsrc, $info['json'][0]['s']);
        $this->assertEquals(implode(',', $keywords), $info['json'][0]['k']);
        $this->assertEquals(json_encode($extras), json_encode($info['json'][0]['x']));
    }

    /**
     * 解析日志文件，文件中必须只有一条日志
     *
     * @param string $file 文件名称
     * @return array
     */
    private function parseLogFile($fileName)
    {
        $data = file_get_contents($fileName);
        $row = trim($data);
        $temp = explode('] ', $row);
        if (3 != count($temp)) {
            return [];
        }
        $time = ltrim($temp[0], '[');
        $date = explode(' ', $time);
        $date = $date[0];
        $logJsonArray = json_decode($temp[1] . ']', true);
        if (empty($logJsonArray)) {
            return [];
        }
        if (empty($temp[2])) {
            return [];
        }
        return [
            'date' => $date,
            'json' => $logJsonArray,
            'message' => $temp[2],
        ];
    }
}
