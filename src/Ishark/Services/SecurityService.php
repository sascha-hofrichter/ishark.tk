<?php

namespace Ishark\Services;


class SecurityService extends BaseService
{
    /**
     * @return String A 8-char-String.
     */
    public function getDeleteToken() {
        return substr(base_convert(md5(uniqid(rand(), true)), 16, 36), 0, 8);
    }

} 