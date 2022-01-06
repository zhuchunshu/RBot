<?php

namespace App\RBot\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;
use Attribute;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
#[Attribute(Attribute::TARGET_CLASS)]
class RBotInit extends AbstractAnnotation{}