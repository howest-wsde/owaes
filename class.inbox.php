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
				$iMe = $this->iMe; 
				$strSQL = "select c.receivers, count(c.id) as total, count(o.isread) as unreadme , count(p.isread) as unreadother , max(c.sentdate) as lastmsg 
								from tblConversations c
								left join tblConversations o on c.id = o.id and o.receivers like '%,$iMe,%' and o.sender != '$iMe' and o.isread = 0 
								left join tblConversations p on c.id = p.id and p.sender = '$iMe' and p.isread = 0
								where 
										c.receivers like '$iMe,%' 
										or c.receivers like '%,$iMe' 
										or c.receivers like '%,$iMe,%' 
										group by c.receivers 
										order by max(c.sentdate) desc "; 
				$oDB = new database($strSQL, TRUE); 
				while ($oDB->nextRecord()){
					$arDiscussion = array(); 
					$arDiscussion["ids"] = $oDB->get("receivers"); 
					$arDiscussion["names"] = ""; 
					$arDiscussion["users"] = array(); 
					$arDiscussion["unread"] = $oDB->get("unreadme"); 
					$arDiscussion["unreadother"] = $oDB->get("unreadother"); 
					$arDiscussion["lastpost"] = $oDB->get("lastmsg"); 
					$arDB = explode(",", $arDiscussion["ids"]); 
					//$arDiscFriends = array(); 
					foreach ($arDB as $iUser) {
						if ($iUser != $iMe) {
							if (!isset($arFriends[$iUser])) $arFriends[$iUser] = user($iUser); 
							$arDiscussion["users"][] = array(
												"id" => $iUser, 
												"name" => $arFriends[$iUser]->getName()
											); 
							$arDiscussion["names"] .= ($arDiscussion["names"] != "") ? ", " . $arFriends[$iUser]->getName() : $arFriends[$iUser]->getName(); 
						}
					}
					$arDiscussions[] = $arDiscussion;
				}
				$this->arDiscussions = $arDiscussions; 
			}  
			return $this->arDiscussions; 
		}
	}
	