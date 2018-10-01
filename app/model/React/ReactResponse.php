<?php

class ReactResponse extends Nette\Object implements Nette\Application\IResponse {
    /**
     * @var ReactMessage[]
     */
    private $messages;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var string
     */
    private $contentType;
    /**
     * @var string
     */
    private $act;


    public function __construct($contentType = null) {
        $this->contentType = $contentType ? $contentType : 'application/json';
        $this->messages = [];
    }

    /**
     * @return string
     */
    final public function getContentType() {
        return $this->contentType;
    }

    /**
     * @param $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * @param ReactMessage[] $messages
     */
    public function setMessages(array $messages) {
        $this->messages = $messages;
    }

    /**
     * @param ReactMessage $message
     */
    public function addMessage(\ReactMessage $message) {
        $this->messages[] = $message;
    }

    public function setAct($act) {
        $this->act = $act;
    }

    /**
     * @param \Nette\Http\IRequest $httpRequest
     * @param \Nette\Http\IResponse $httpResponse
     * @throws \Nette\Utils\JsonException
     */
    public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse) {
        $httpResponse->setContentType($this->contentType);
        $httpResponse->setExpiration(FALSE);
        $response = [
            'messages' => array_map(function (ReactMessage $value) {
                return $value->__to[];
            }, $this->messages),
            'act' => $this->act,
            'data' => $this->data,
        ];
        echo Nette\Utils\Json::encode($response);
    }
}
