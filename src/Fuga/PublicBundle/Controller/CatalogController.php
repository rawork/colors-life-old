<?php

namespace Fuga\PublicBundle\Controller;

use Fuga\CommonBundle\Controller\PublicController;

class CatalogController extends PublicController {
	
	public function __construct() {
		parent::__construct('catalog');
	}


	public function advertAction($params) {
		$items = $this->get('container')->getItems(
			'catalog_commercial', 
			'publish=1', 
			null, 
			5
		);
		
		return $this->render('catalog/advert.tpl', compact('items'));
	}
	
	public function brandAction($params) {
		if (!isset($params[0])) {
			throw $this->createNotFoundException('Несуществующая страница');
		}
		$page = $this->get('util')->_getVar('page', true, 1);
		$producer = $this->get('container')->getItem('catalog_producer', $params[0]);
		if (!$producer) {
			throw $this->createNotFoundException('Несуществующая страница');
		}
		$paginator = $this->get('paginator');
		$paginator->paginate(
				$this->get('container')->getTable('catalog_product'),
				$this->get('container')->href($this->get('router')->getParam('node'), 'brand', $params).'?page=###',
				'publish=1 AND producer_id='.$producer['id'],  
				12,
				$page
		);
		$paginator->setTemplate('public');
		$items = $this->get('container')->getItems(
			'catalog_product', 
			'publish=1 AND producer_id='.$producer['id'], 
			null, 
			$paginator->limit
		);
		$this->get('container')->setVar('title', $producer['name']);
		$this->get('container')->setVar('h1', $producer['name']);
		
		return $this->render('catalog/brand.tpl', compact('producer', 'items', 'paginator'));
	}
	
	public function brandsAction($params) {
		$items = $this->get('container')->getItems(
			'catalog_producer', 
			'publish=1', 
			null, 
			100
		);
		$this->get('container')->setVar('title', 'Бренды');
		$this->get('container')->setVar('h1', 'Бренды');
		
		return $this->render('catalog/brands.tpl', compact('items'));
	}
	
	public function catsAction($params) {
		$items =  $this->get('container')->getItems(
			'catalog_category',
			'publish=1 AND parent_id=0'
		);
		
		return $this->render('catalog/cats.tpl', compact('items'));
	}
	
	public function hitAction($params) {
		$hits = null;
		if ('stuff' == $this->get('router')->getParam('action')) {
			$item = $this->get('container')->getItem('catalog_product', $params[0]);
			$ids = implode(',', array_keys($this->get('container')->getItemsRaw(
					'SELECT p.id FROM catalog_category c 
					JOIN catalog_product p ON c.id=p.category_id 
					WHERE p.publish=1 AND p.is_hit=1 
					AND c.root_id='.$item['category_id_root_id'].' AND p.id<>'.$item['id']
			)));
			$hits = $this->get('container')->getItems(
				'catalog_product', 
				'id IN ('.$ids.') AND publish=1 AND is_hit=1',
				'RAND()',
				$this->getParam('limit_hit')
			);		
		} else {
			if (isset($params[0])) {
				
				$ids = implode(',', array_keys($this->get('container')->getItemsRaw(
					'SELECT p.id FROM catalog_category c 
					JOIN catalog_product p ON c.id=p.category_id 
					WHERE p.publish=1 AND p.is_hit=1 AND c.root_id='.$params[0]
				)));		
				$hits = $this->get('container')->getItems(
					'catalog_product', 
					'id IN ('.$ids.') AND publish=1 AND is_hit=1',
					'RAND()',
					$this->getParam('limit_hit')
				);
			} else {
				$hits = $this->get('container')->getItems(
					'catalog_product', 
					'publish=1 AND is_hit=1',
					'RAND()',
					$this->getParam('limit_hit')
				);
			}
		}
		if ($hits) {
			return $this->render('catalog/hit.tpl', compact('hits'));
		} else {
			return;
		}
	}
	
	public function indexAction($params) {
		$catId = isset($params[0]) ? $params[0] : 0;
		$cats = $this->getManager('Fuga:Common:Category')->getCategoryTree($catId);
		$per_column = ceil(count($cats)/2);
		$cat = null;
		if (isset($params[0])) {
			$cat = $this->get('container')->getItem('catalog_category', 'publish=1 AND id='.$params[0]);
			if (!$cat) {
				throw $this->createNotFoundException('Несуществующая страница');
			}
			$this->get('container')->setVar('title', $cat['title']);
			$this->get('container')->setVar('h1', $cat['title']);
			
			$sort = isset($params[1]) ? $params[1] : 'sort';
			if ($sort != 'sort' && $sort != 'price' && $sort != 'name') {
				$sort = 'sort';
			}
			$rtt = $this->get('util')->_getVar('rtt', true, 6);
			if ($rtt > 48 || $rtt < 6) {
				$rtt = 1000;
			}
			
			$products = implode(',', array_keys($this->get('container')->getItemsRaw(
				'SELECT product_id as id FROM catalog_products_categories WHERE category_id='.$cat['id'] 
			)));
			if ($products) {
				$products = ' OR id IN('.$products;
			}

			$page = $this->get('util')->_getVar('page', true, 1);
			$paginator = $this->get('paginator');
			$paginator->paginate(
				$this->get('container')->getTable('catalog_product'),
				$this->get('container')->href($this->get('router')->getParam('node'), 'index', array($cat['id'],$sort)).'?page=###&rtt='.$rtt,
				'(category_id='.$cat['id'].$products.') AND publish=1',
				$rtt,
				$page
			);
			$paginator->setTemplate('public');

			$products = $this->get('container')->getItems(
					'catalog_product',
					'(category_id='.$cat['id'].$products.') AND publish=1',
					'is_exist DESC,'.$sort,	
					$paginator->limit	
			);
		}
		
		return $this->render('catalog/index.tpl', compact('cats', 'cat', 'per_column', 'products', 'paginator', 'rtt', 'sort'));
	}
	
	public function newAction($params) {
		$items =  $this->get('container')->getItems(
			'catalog_product', 
			'publish=1 AND is_new=1', 
			null, 
			$this->getParam('limit_new')
		);
		
		return $this->render('catalog/new.tpl', compact('items'));
	}
	
	public function offerAction($params) {
		$items =  $this->get('container')->getItems(
			'catalog_product', 
			'publish=1 AND is_discount=1', 
			null, 
			$this->getParam('limit_spec')
		);
		
		return $this->render('catalog/offer.tpl', compact('items'));
	}
	
	public function partnersAction($params) {
		$items =  $this->get('container')->getItems(
			'catalog_partner', 
			'publish=1' 
		);
		
		return $this->render('catalog/partners.tpl', compact('items'));
	}
	
	public function promotionAction($params) {
		if (!isset($params[0])) {
			throw $this->createNotFoundException('Несуществующая страница');
		}
		$item = $this->get('container')->getItem('catalog_commercial', 'id='.$params[0].' AND publish=1');
		if (!$item) {
			throw $this->createNotFoundException('Несуществующая страница');
		}
		$this->get('container')->setVar('title', $item['name']);
		$this->get('container')->setVar('h1', $item['name']);
		
		return $this->render('catalog/promotion.tpl', compact('item'));
	}
	
	public function promotionsAction($params) {
		$items =  $this->get('container')->getItems('catalog_commercial', 'publish=1');
		$this->get('container')->setVar('title', 'Акции и скидки');
		$this->get('container')->setVar('h1', 'Акции и скидки');
		
		return $this->render('catalog/promotions.tpl', compact('items'));
	}
	
	public function searchAction($params) {
		return $this->render('catalog/search.tpl');
	}
	
	public function selectorsAction($params) {
		$cats = $this->getManager('Fuga:Common:Category')->getCategoryTree();
		
		return $this->render('catalog/selectors.tpl', compact('cats'));
	}
	
	public function stuffAction($params) {
		if (!isset($params[0])) {
			throw $this->createNotFoundException('Несуществующая страница');
		}
		$item = $this->get('container')->getItem('catalog_product', 'id='.$params[0].' AND publish=1');
		if (!$item) {
			throw $this->createNotFoundException('Несуществующая страница');
		}
		$this->get('container')->setVar('title', $item['name']);
		$this->get('container')->setVar('h1', $item['name']);
		$cat0 = $this->get('container')->getItem('catalog_category', $item['category_id_root_id']);
		$prices =  $this->get('container')->getItems(
			'catalog_price', 
			'product_id='.$item['id'].' AND publish=1', 
			'sort,size_id'
		);
		$fotos =  $this->get('container')->getItemsRaw("SELECT * FROM system_files WHERE table_name='catalog_product' AND entity_id=".$item['id']." ORDER BY created");
		$articles = $this->get('container')->getItemsRaw("SELECT a.id, a.name FROM article_products_articles st_ar JOIN article_article a ON ar.id=st_ar.article_id WHERE st_ar.product_id=".$item['id']." GROUP BY st_ar.article_id");
		
		return $this->render('catalog/stuff.tpl', compact('item', 'cat0', 'prices', 'fotos', 'articles'));
	}
	
	
}