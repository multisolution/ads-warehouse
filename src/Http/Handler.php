<?php declare(strict_types=1);

namespace AdsWarehouse\Http;

use AdsWarehouse\Context;
use GraphQL\Error\Debug;
use GraphQL\Error\FormattedError;
use Siler\Monolog as Log;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Throwable;
use UnexpectedValueException;
use function Siler\Encoder\Json\decode;
use function Siler\GraphQL\debug;
use function Siler\GraphQL\execute;
use function Siler\Swoole\{cors, json, raw};

class Handler
{
    /** @var Context */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;

        debug($context->debug ? Debug::INCLUDE_DEBUG_MESSAGE : 0);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws Throwable
     */
    public function __invoke(Request $request, Response $response): void
    {
        try {
            $raw = raw();

            if ($raw === null) {
                throw new UnexpectedValueException('Empty content');
            }

            /** @var array<string, mixed> $input */
            $input = decode($raw);
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
