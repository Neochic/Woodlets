<?php

namespace Neochic\Woodlets;

class NoticeManager
{
    protected $twig;
    protected $wpWrapper;

    public function __construct($wpWrapper, $twig) {
        $this->twig = $twig;
        $this->wpWrapper = $wpWrapper;
    }

    protected function notice($class, $message) {
        $this->wpWrapper->addAction( 'admin_notices', function() use ($class, $message) {
            echo $this->twig->render('@woodlets/notice.twig', array('class' => $class, 'message' => $message));
        });
    }

    public function error($message) {
        $this->notice('error', $message);
    }

    public function updated($message) {
        $this->notice('updated', $message);
    }

    public function updateNag($message) {
        $this->notice('update-nag', $message);
    }
}