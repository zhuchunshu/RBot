<?php

namespace App\RBot\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
#[Attribute(Attribute::TARGET_METHOD)]
class RBotOnMessage extends AbstractAnnotation{
    public string $post_type;
    public string|null $message_type=null;
    public string|null $notice_type=null;
    public string|null $request_type=null;
}
