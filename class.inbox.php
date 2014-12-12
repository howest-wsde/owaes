<?php 
	class inbox {   /* wordt nog gebruikt in class.page en conversation.php, maar moet vervangen worden door class.messages */
		private $iMe = 0; 
		private $arDiscussions = NULL; 
		 
		public function inbox () { 
			global $oPage;  
			$this->iMe = $oPage->iUser; 
			
		}
		 
		public function discussions() {
			if (is_null($this->arDiscussions)) {
				$arFriends = array(); 
				$arDiscussions = array(); 
				$iMe = me(); 
				$strSQL = "select c.receiver, c.sender, count(c.id) as total, count(o.isread) as unread , max(c.sentdate) as lastmsg 
								from tblConversations c
								left join tblConversations o on c.id = o.id and o.isread = 0 
								where 
										c.receiver = '$iMe' or c.sender = '$iMe'  
										group by c.receiver, c.sender
										order by max(c.sentdate) desc "; 
				$oDB = new database($strSQL, TRUE); 
				while ($oDB->nextRecord()){
					$iUser = ($oDB->get("sender") == me()) ? $oDB->get("receiver") : $oDB->get("sender");
					if (!isset($arDiscussions[$iUser])) $arDiscussions[$iUser] = array("unread" => array("total"=>0, "other"=>0, "me"=>0)); 
					$arDiscussions[$iUser]["unread"]["total"] += $oDB->get("unread"); 
					$arDiscussions[$iUser]["unread"][($oDB->get("sender") == me()) ? "other" : "me"] = intval($oDB->get("unread")); 
					$arDiscussions[$iUser]["lastpost"] = $oDB->get("lastmsg");  
					$arDiscussions[$iUser]["name"] = user($iUser)->getName();  
				}
				$this->arDiscussions = $arDiscussions; 
			}  
			return $this->arDiscussions; 
		}
	}
	