<?php

namespace App\Core;

class ViewResponse
{
    protected string $view;
    protected array $data = [];

    public function __construct(string $view, array $data = [])
    {
        $this->view = $view;
        $this->data = $data;
    }

    public function with(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function render()
    {
        return Template::render($this->view, $this->data);
    }
}
