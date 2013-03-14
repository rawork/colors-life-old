<?php

namespace Fuga\CommonBundle\Model;

use Fuga\Component\Form\Widget\DiagramWidget;

class VoteManager extends ModelManager {
	
	protected $entityTable = 'vote_question';
	protected $answerTable = 'vote_answer';
	protected $cacheTable = 'vote_cache';
	
	// TODO убрать отсюда
	function getForm($voteName) {
		$vote = $this->get('container')->getItem(
				$this->entityTable, 
				"name='".$voteName."' AND datefrom<'".date('Y-m-d H:i:s')."' AND datetill>'".date('Y-m-d H:i:s')."'"
		);
		if ($vote) {
			$answers = $this->get('container')->getItems(
				$this->answerTable,
				'question_id='.$vote['id']." AND publish=1"
			);		
			return $this->get('templating')->render('vote/form.tpl', compact('vote', 'answers'));
		} else {
			return '';
		}

	}

	function updateData($data = null) {
		$error = null;
		if (!$data) {
			return $error;
		}
		$answerId = isset($data['answer']) ? intval($data['answer']) : 0;
		$voteName = isset($data['vote']) ? intval($data['vote']) : null;
		if ($voteName && $answerId){
			$cache = 0;
			$vote = $this->get('container')->getItem($this->entityTable, 'name="'.$voteName.'"');
			if ($vote['lmt'] == 1) {
				$cache = $this->get('container')->count($this->cacheTable, "sessionid='".session_id()."' AND time>".(time()-$vote['step']));
			} elseif ($vote['lmt'] == 2) {
				$cache = $this->get('container')->count($this->cacheTable, "(sessionid='".session_id()."' OR ip='".$_SERVER['REMOTE_ADDR']."') AND time>".(time()-$vote['step']));
			}
			if (!$cache) {
				$this->get('container')->addItem(
					$this->cacheTable, array(
						'ip' => $_SERVER['REMOTE_ADDR'],
						'sessionid' => session_id(),
						'question_id' => $vote['id'],
						'time' => time()
				));
				$this->get('connection1')->query('UPDATE '.$this->entityTable.' SET quantity=quantity+1 WHERE id='.$vote['id']);
				$this->get('connection1')->query('UPDATE '.$this->answerTable.' SET quantity=quantity+1 WHERE id=',$answerId);
			} else {
				$error = 'Количество голосований ограничено';
			}
		}
		return $error;
	}
	
	// TODO убрать отсюда 
	function getResult($voteName, $formData = null) {
		$error = $this->updateData($formData);
		$vote = $this->get('container')->getItem($this->entityTable, 'name="'.$voteName.'"');
		if ($vote) {
			$answers = $this->get('container')->getItems($this->answerTable, 'question_id='.$vote['id']." AND publish=1");
			$rows = array();
			foreach ($answers as &$answer){
				$answer['percent'] = round($answer['quantity'] ? intval($answer['quantity'])/intval($vote['quantity'])*100 : 0, 2);
				if ($answer['quantity'])
					$rows[] = array(intval(360*$answer['percent']/100), $answer['color']); 
			}
			$diagram = null;
			if ($vote['is_dia']) {
				$diagram = new DiagramWidget();
				$diagram->bgcolor = 'FFFFFF';
				$diagram->draw($rows);
			}
			return $this->get('templating')->render('vote/result.tpl', compact('answers', 'vote', 'error', 'diagram'));
		} else {
			return '';
		}
	}

}
