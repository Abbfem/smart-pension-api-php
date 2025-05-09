<?php

namespace SMART\Request;

use SMART\Exceptions\EmptyServerTokenException;
use SMART\ServerToken\ServerToken;

abstract class RequestWithServerToken extends Request
{
    /**
     * @throws EmptyServerTokenException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \SMART\Response\Response
     */
    public function fire()
    {
        $this->checkServerToken();

        return parent::fire();
    }

    protected function getHeaders(): array
    {
        return array_merge(parent::getHeaders(), [
            RequestHeader::AUTHORIZATION => $this->getAuthorizationHeader(ServerToken::getInstance()->get()),
        ]);
    }

    /**
     * @throws EmptyServerTokenException
     */
    private function checkServerToken()
    {
        if (is_null(ServerToken::getInstance()->get())) {
            throw new EmptyServerTokenException('Server token is empty, please set using ServerToken::getInstance()->set() method.');
        }
    }
}
