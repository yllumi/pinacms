<?php
/*
* PinaCMS
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
* A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
* OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
* SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
* LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
* DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
* THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
* OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
* @copyright © 2010 Dobrosite ltd.
*/
if (!defined('PATH')){ exit; }



require_once PATH_CORE.'classes/BaseFinder.php';
require_once PATH_CORE.'classes/ExtManager.php';

class SiteFinder extends BaseFinder
{
	function search($rules, $sorting, $paging)
	{
		$db = getDB();

                $this->addField('`cody_site`.*');
                $this->setFrom('`cody_site`');

		if (!empty($rules['substring']))
		{
			$this->addWhere("
				site_domain LIKE '%".$rules["substring"]."%' OR
				site_path LIKE '%".$rules["substring"]."%' OR
				site_template LIKE '%".$rules["substring"]."%'
			");
		}

		$this->addSorting($sorting);

		if (!empty($paging))
		{
			$paging->setTotal(
				$db->one(
					$this->constructSelect(true)
				)
			);
			$this->setPaging($paging);
		}
		$sql = $this->constructSelect();
		#echo $sql;die;
		return $db->table($sql);
	}

        private function addSorting($sorting)
        {
		if (empty($sorting)) $sorting = new Sorting("", "");

		$sortField = $sorting->getField();
		$sortDir = $sorting->getDirection();

		// Фильтруем поле сортировки
		$sortTable = 'cody_site';
		if (!in_array($sortField, array('id', 'domain', 'path', 'template', 'account_id')))
		{
			$sortField = 'site_id';
			$sortDir   = 'desc';
		}
		else
		{
			$sortField = 'site_'.$sortField;
		}

		// Фильтруем направление сортировки
		if ($sortDir != 'asc' && $sortDir != 'desc' || $sortDir == '')
		{
			$sortDir = 'desc';
		}

		// Конструируем SELECT-запрос
		$this->addOrderBy($sortTable.'.'.$sortField.' '.$sortDir);
		if ($sortField != "site_id") $this->addOrderBy('cody_site.site_id desc');
        }
}