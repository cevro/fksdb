<?php

namespace FKSDB\Models\Exports\Formats;

use Nette\Application\Response;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\SmartObject;

class PlainTextResponse implements Response {
    use SmartObject;

    private string $content;

    private string $name;

    public function __construct(string $content) {
        $this->content = $content;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function send(IRequest $httpRequest, IResponse $httpResponse): void {
        $httpResponse->setContentType('text/plain', 'utf-8');

        if ($this->name) {
            $httpResponse->setHeader('Content-Disposition', 'attachment; filename="' . $this->name . '"');
        } else {
            $httpResponse->setHeader('Content-Disposition', 'attachment');
        }

        $httpResponse->setHeader('Content-Length', strlen($this->content));
        echo $this->content;
    }

}
