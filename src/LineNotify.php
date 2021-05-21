<?php
namespace WithKao;

use Illuminate\Support\Facades\Http;

class LineNotify
{
    protected $token = null;
    protected $messages = [];
    protected $thumbnailUrl = null;
    protected $imageUrl = null;
    protected $imagePath = null;
    protected $notifyDisabled = false;
    protected $stickerPackageId = null;
    protected $stickerId = null;

    public const URL = 'https://notify-api.line.me/api/notify';

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function setToken(string $token): LineNotify
    {
        $this->token = $token;
        return $this;
    }

    public function addMessage(string $message): LineNotify
    {
        $this->messages[] = $message;
        return $this;
    }

    public function thumbnailUrl(string $url): LineNotify
    {
        $this->thumbnailUrl = $url;
        return $this;
    }

    public function imageUrl(string $url): LineNotify
    {
        $this->imageUrl = $url;
        return $this;
    }

    public function imagePath(string $path): LineNotify
    {
        $this->imagePath = $path;
        return $this;
    }

    public function sticker(int $stickerPackageId, int $stickerId): LineNotify
    {
        $this->stickerPackageId = $stickerPackageId;
        $this->stickerId = $stickerId;
        return $this;
    }

    public function disableNotify(): LineNotify
    {
        $this->notifyDisabled = true;
        return $this;
    }

    public function enableNotify(): LineNotify
    {
        $this->notifyDisabled = false;
        return $this;
    }

    private function getMessage(): string
    {
        if (empty($this->messages)) {
            throw new LineNotifyException('No message specified!');
        }
        return implode(PHP_EOL, $this->messages);
    }

    public function send($message = null): bool
    {
        if ($this->token === null) {
            throw new LineNotifyException(
                'Token not specified!',
                'Please specify access token in config/line-notify.php or set LINE_ACCESS_TOKEN in .env or use setToken function'
            );
        }

        if ($message !== null) {
            $this->addMessage($message);
        }

        $client = Http::withToken($this->token);

        if ($this->imagePath !== null) {
            $client = $client->attach('imageFile', fopen($this->imagePath, 'r'));
        }

        $params = [
            'message' => $this->getMessage(),
            'notificationDisabled' => $this->notifyDisabled ? 1 : 0
        ];

        if ($this->imageUrl !== null) {
            $params['imageThumbnail'] = $this->thumbnailUrl ?? $this->imageUrl;
            $params['imageFullsize'] = $this->imageUrl;
        }

        if ($this->stickerId !== null) {
            $params['stickerPackageId'] = $this->stickerPackageId;
            $params['stickerId'] = $this->stickerId;
        }

        $response = $client->post(static::URL, $params);
        return $response->successful()
            && $response->json()['status'] === 200;
    }
}
