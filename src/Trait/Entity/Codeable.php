<?php

namespace App\Trait\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @method getCodeableName() it expects to be declared in the usage class
 *
 * @property string $code it expects to be declared in the usage class
 */
trait Codeable
{
    #[ORM\PrePersist()]
    public function setupCode(): void
    {
        if (!empty($this->code)) {
            return;
        }

        $slug = strtolower($this->getCodeableName());
        $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
        $slug = trim($slug, '_');

        $this->code = $slug;
    }
}
