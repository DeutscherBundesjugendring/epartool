<?php

class Dbjr_Api_BaseController extends Zend_Controller_Action
{
    const HTTP_STATUS_OK = 200;

    const HTTP_STATUS_BAD_REQUEST = 400;
    const HTTP_STATUS_NOT_FOUND = 404;
    const HTTP_STATUS_METHOD_NOT_ALLOWED = 405;

    const HTTP_STATUS_SERVER_ERROR = 500;

    /**
     * @param array $definition
     * @return array
     * @throws Dbjr_Api_Exception
     */
    protected function getParameters($definition)
    {
        $result = [];
        foreach ($definition as $parameter) {
            $result[] = $value = $this->getRequest()->getParam($parameter);
            if ($value === null) {
                throw new Dbjr_Api_Exception(
                    self::HTTP_STATUS_BAD_REQUEST,
                    sprintf('Required parameter %s is missing', $parameter)
                );
            }
        }

        return $result;
    }

    /**
     * @param int $httpStatusCode
     * @param string $message
     */
    protected function sendError($httpStatusCode, $message)
    {
        $this->buildResponse($httpStatusCode, ['error' => $message]);
    }

    /**
     * @param int $httpStatusCode
     * @param array $data
     */
    protected function buildResponse($httpStatusCode, array $data)
    {
        $this->getResponse()->setHttpResponseCode($httpStatusCode);
        $this->_helper->json($data);
    }
}
