<?php
/**
 * Created by PhpStorm.
 * User: runs
 * Date: 18-12-30
 * Time: 下午9:43
 */

namespace EasySwoole\WeChat\OfficialAccount;

use EasySwoole\WeChat\Bean\OfficialAccount\MediaRequest;
use EasySwoole\WeChat\Bean\OfficialAccount\MediaResponse;
use EasySwoole\WeChat\Exception\OfficialAccountError;
use EasySwoole\WeChat\Utility\HttpClient;
use EasySwoole\WeChat\Utility\PostFile;

class Media extends OfficialAccountBase
{
    /**
     * @param MediaRequest $mediaBean
     * @return mixed
     * @throws OfficialAccountError
     */
    public function upload(MediaRequest $mediaBean)
    {
        $url = ApiUrl::generateURL(ApiUrl::MEDIA_UPLOAD, [
            'ACCESS_TOKEN'=> $this->getOfficialAccount()->accessToken()->getToken(),
            'TYPE' => $mediaBean->getType()
        ]);

        $fileBean = $this->crateFileBean($mediaBean);

        // 视频类型额外参数
        if ($mediaBean->getType() === MediaRequest::TYPE_VIDEO) {
            $form = ['description' => $mediaBean->getDescription()];
        }

        $json = HttpClient::postFileForJson($url, $fileBean, $form ?? null);
        $ex = OfficialAccountError::hasException($json);
        if($ex){
            throw $ex;
        }

        return $json;
    }

    /**
     * @param $mediaId
     * @return MediaResponse || array
     * @throws OfficialAccountError
     */
    public function get($mediaId)
    {
        $url = ApiUrl::generateURL(ApiUrl::MEDIA_GET, [
            'ACCESS_TOKEN'=> $this->getOfficialAccount()->accessToken()->getToken(),
            'MEDIA_ID' => $mediaId
        ]);

        $response = HttpClient::get($url);

        if (empty($response->getBody()) || '{' === $response->getBody()[0]) {
            $body = json_decode($response->getBody(), true);
            $ex = OfficialAccountError::hasException($body);
            if ($ex) {
                throw $ex;
            }
            return $body;
        }
        return new MediaResponse($response);
    }

    /**
     * @param MediaRequest $mediaBean
     * @return PostFile
     */
    private function crateFileBean(MediaRequest $mediaBean) : PostFile
    {
        $fileBean = new PostFile($mediaBean->toArray(null, MediaRequest::FILTER_NOT_EMPTY));
        $fileBean->setName('media');

        if ($fileBean->getData() !== null) {
            $fileBean->setFilename($fileBean->getName(). File::getStreamExt($fileBean->getData()));
            $fileBean->setMimeType(File::getStreamMimeType($fileBean->getData()));
        }

        return $fileBean;
    }
}