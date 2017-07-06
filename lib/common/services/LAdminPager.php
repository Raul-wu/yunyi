<?php
class LAdminPager
{
	public static function getPage($curPage, $lastPage)
	{
		$pageTemp = '<div class="paginator_mod fr"> <ul class="pure-paginator" > {pageHtml}</ul></div>';
		$pageNode = '<li><a {pageUrl}  {pageClass} >{pageStr}</a></li>';
		$classDisabled = 'class="pure-button"';
		$classCurrent = 'class= "pure-button pure-button-active" ';
		$pageHtml = '';
        $url = preg_replace("/page=[0-9]+&?/", "", Yii::app()->request->getUrl());
        $operator = '';

        if (strpos($url, '?') === false)
        {
            $operator = '?';
        }
        else if (substr($url, -1, 1) != '?' && substr($url, -1, 1) != '&')
        {
            $operator = '&';
        }

		if($lastPage > 1)
		{
			$pageHtml .= str_replace(
				array('{pageClass}', '{pageUrl}', '{pageStr}'),
				array($curPage <= 1 ? $classDisabled : 'class="pure-button prev"',
					$curPage <= 1 ? '' : 'href="' . $url . $operator .'page=' . ($curPage - 1) . '"',
					'&#171;'
				),
				$pageNode
			);
			$pageHtml .= str_replace(
				array('{pageClass}', '{pageUrl}', '{pageStr}'),
				array($curPage == 1 ? $classCurrent : 'class="pure-button" ', $curPage == 1 ? '' : 'href="'.$url. $operator .'page=1'.'"', 1),
				$pageNode
			);

			$prefix = 2;

			$prefixed = false;
			$nextfixed = false;

			$startPage = $curPage - $prefix;
			if ($startPage <= 2)
			{
				$startPage = 2;
			}
			else
			{
				$prefixed = true;
			}

			$endPage = $curPage + $prefix;
			if ($endPage >= $lastPage - 1)
			{
				$endPage = $lastPage - 1;
			}
			else
			{
				$nextfixed = true;
			}

			if ($prefixed)
			{
				$pageHtml .= str_replace(
					array('{pageClass}', '{pageUrl}', '{pageStr}'),
					array($classDisabled, '', '...'),
					$pageNode
				);
			}

			for ($i = $startPage; $i <= $endPage; $i++)
			{
				$pageHtml .= str_replace(
					array('{pageClass}', '{pageUrl}', '{pageStr}'),
					array($curPage == $i ? $classCurrent : 'class="pure-button"',
						$curPage == $i ? '' : 'href="'.$url. $operator .'page='.$i.'"',
						$i
					),
					$pageNode
				);
			}

			if($nextfixed)
			{
				$pageHtml .= str_replace(
					array('{pageClass}', '{pageUrl}', '{pageStr}'),
					array($classDisabled, '', '...'
					),
					$pageNode
				);
			}

			$pageHtml .= str_replace(
				array('{pageClass}', '{pageUrl}', '{pageStr}'),
				array($curPage == $lastPage ? $classCurrent : 'class="pure-button"',
					$curPage == $lastPage ? '' : 'href="' . $url . $operator .'page=' . $lastPage . '"',
					$lastPage
				),
				$pageNode
			);

			$pageHtml .= str_replace(
				array('{pageClass}', '{pageUrl}', '{pageStr}'),
				array($curPage == $lastPage ? $classDisabled : 'class="pure-button next"',
					$curPage == $lastPage ? '' : 'href="' . $url . $operator .'page=' . ($curPage + 1) . '"',
					'&#187;'
				),
				$pageNode
			);
		}

		return str_replace('{pageHtml}', $pageHtml, $pageTemp);
	}

    /**
     * 分页
     * @param $count  总条数
     * @param int $page 当前页
     * @param int $perpage 每页条数
     * @param string $url 跳转网址
     * @return mixed
     */
    public static function getPages($count, $page = 1, $perpage = 20, $url = "")
    {
        $pageTemp = '<div class="paginator_mod"> <ul class="pure-paginator" style="display:inline-block"> {pageHtml}{jump}</ul></div>';
        $pageNode = '<li><a {pageUrl}  {pageClass} >{pageStr}</a></li>';
        $classDisabled = 'class="pure-button"';
        $classCurrent = 'class= "pure-button pure-button-active" ';
        $pageHtml = '';
        $url = !empty($url) ? $url.'&' : $url.'?';
        $jump = '';
        $lastPage = ceil($count / $perpage);
        $curPage = $page;
        if ($lastPage < $page)
        {
            $curPage = $lastPage;
        }

        if($lastPage > 1)
        {
            $pageHtml .= str_replace(
                array('{pageClass}', '{pageUrl}', '{pageStr}'),
                array($curPage <= 1 ? $classDisabled : 'class="pure-button prev"',
                    $curPage <= 1 ? '' : 'href="' . $url . 'page=' . ($curPage - 1) . '"',
                    '&#171;'
                ),
                $pageNode
            );
            $jump = '<input name="page" id="page" style="width:30px;vertical-align: top;height: 32px;margin: 0 5px;" type=\'text\' value="'.Yii::app()->request->getParam("page", 1).'"/><input type=\'button\' value=\'跳转\' style="height: 38px;vertical-align: top;" onclick="jump()"/><script>function jump() {var url = window.location.href.replace(/page=.*/,""); var page = document.getElementById("page").value; page=page < 1 ? 1 : page; url = url.indexOf("?") == -1 ? url + "?page=" + page : url + "page=" + page; location.href=url;}</script>';
            $pageHtml .= str_replace(
                array('{pageClass}', '{pageUrl}', '{pageStr}'),
                array($curPage == 1 ? $classCurrent : 'class="pure-button" ', $curPage == 1 ? '' : 'href="'.$url.'page=1'.'"', 1),
                $pageNode
            );

            $prefix = 2;

            $prefixed = false;
            $nextfixed = false;

            $startPage = $curPage - $prefix;
            if ($startPage <= 2)
            {
                $startPage = 2;
            }
            else
            {
                $prefixed = true;
            }

            $endPage = $curPage + $prefix;
            if ($endPage >= $lastPage - 1)
            {
                $endPage = $lastPage - 1;
            }
            else
            {
                $nextfixed = true;
            }

            if ($prefixed)
            {
                $pageHtml .= str_replace(
                    array('{pageClass}', '{pageUrl}', '{pageStr}'),
                    array($classDisabled, '', '...'),
                    $pageNode
                );
            }

            for ($i = $startPage; $i <= $endPage; $i++)
            {
                $pageHtml .= str_replace(
                    array('{pageClass}', '{pageUrl}', '{pageStr}'),
                    array($curPage == $i ? $classCurrent : 'class="pure-button"',
                        $curPage == $i ? '' : 'href="'.$url.'page='.$i.'"',
                        $i
                    ),
                    $pageNode
                );
            }

            if($nextfixed)
            {
                $pageHtml .= str_replace(
                    array('{pageClass}', '{pageUrl}', '{pageStr}'),
                    array($classDisabled, '', '...'
                    ),
                    $pageNode
                );
            }

            $pageHtml .= str_replace(
                array('{pageClass}', '{pageUrl}', '{pageStr}'),
                array($curPage == $lastPage ? $classCurrent : 'class="pure-button"',
                    $curPage == $lastPage ? '' : 'href="' . $url . 'page=' . $lastPage . '"',
                    $lastPage
                ),
                $pageNode
            );

            $pageHtml .= str_replace(
                array('{pageClass}', '{pageUrl}', '{pageStr}'),
                array($curPage == $lastPage ? $classDisabled : 'class="pure-button next"',
                    $curPage == $lastPage ? '' : 'href="' . $url . 'page=' . ($curPage + 1) . '"',
                    '&#187;'
                ),
                $pageNode
            );
        }

        return str_replace('{jump}', $jump, str_replace('{pageHtml}', $pageHtml, $pageTemp));
    }
	
}