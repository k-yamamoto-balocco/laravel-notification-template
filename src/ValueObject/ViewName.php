<?php


namespace GitBalocco\LaravelNotificationTemplate\ValueObject;


use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\View\ViewFinderInterface;
use Illuminate\View\ViewName as IlluminateViewName;

class ViewName extends StringValue
{
    /** @var string $path */
    private $path;
    /** @var ViewFinderInterface $viewFinder */
    private $viewFinder;

    /**
     * ViewName constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->viewFinder = App::make(Factory::class)->getFinder();
        parent::__construct($value);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $value
     */
    protected function setValue(string $value): void
    {
        $path = $this->find($value);
        $this->path = $path;
        $this->value = $value;
    }

    /**
     * @param string $template
     * @return string
     */
    private function find(string $template)
    {
        return $this->viewFinder->find($this->normalize($template));
    }

    /**
     * @param string $template
     * @return string
     * @codeCoverageIgnore
     */
    private function normalize(string $template)
    {
        return IlluminateViewName::normalize($template);
    }
}