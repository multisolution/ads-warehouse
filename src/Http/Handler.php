<?php declare(strict_types=1);

namespace AdsWarehouse\Http;

use AdsWarehouse\Context;
use GraphQL\Error\FormattedError;
use Siler\Monolog as Log;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Throwable;
use function Siler\Encoder\Json\decode;
use function Siler\GraphQL\execute;
use function Siler\Swoole\{cors, json, raw};

class Handler
{
    /** @var Context */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function __invoke(Request $request, Response $response)
    {
        try {
            $input = decode(raw());
            $result = execute($this->context->schema, $input, $this->context->rootValue, $this->context);
        } catch (Throwable $exception) {
            Log\error('Internal error', ['exception' => $exception]);
            $result = FormattedError::createFromException($exception);
        } finally {
            cors();
            json($result);
        }
    }
}
