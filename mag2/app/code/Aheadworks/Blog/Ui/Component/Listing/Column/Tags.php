<?php
namespace Aheadworks\Blog\Ui\Component\Listing\Column;

class Tags extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Prepare Tags
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $post) {
                if (is_array($post['tags'])) {
                    $post['tags'] = implode(', ', $post['tags']);
                }
            }
        }
        return $dataSource;
    }
}