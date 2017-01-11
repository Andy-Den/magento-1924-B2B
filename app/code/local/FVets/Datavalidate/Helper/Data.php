<?php

class FVets_Datavalidate_Helper_Data extends Mage_Core_Helper_Data
{
	public function getWebsiteCollection()
	{
		$collection = Mage::getModel('core/website')->getResourceCollection();
		return $collection->load();
	}

	private function getSlackBaseHookUrl()
	{
		return "https://hooks.slack.com/services/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";
	}

	private function getSlackBaseFileUrl()
	{
		return "https://slack.com/api/files.upload";
	}

	private function getChannelsListUrl()
	{
		return "https://slack.com/api/channels.list";
	}

	private function getChannelCreateUrl()
	{
		return "https://slack.com/api/channels.create";
	}

	private function getChannelArquiveUrl()
	{
		return "https://slack.com/api/channels.archive";
	}

	private function getSlackToken()
	{
		return "xoxp-3793260094-3791523309-4815941961-588a4c";
	}

	public function sendSlackMessage($channel, $message, $emoji = '')
	{
		try
		{
			$data = "payload=" . json_encode(array(
					"channel" => "#{$channel}",
					'icon_emoji' => $emoji,
					"text" => $message
				));
			$ch = curl_init($this->getSlackBaseHookUrl());
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
		} catch (Exception $ex)
		{
			$this->log($ex->toString());
		}
	}

	public function sendSlackFile($title, $comment, $filePath, $filetype, $channel)
	{
		try
		{
			$fileName = basename($filePath);
			$fileContent = file_get_contents($filePath);

			$postfields = array("token" => $this->getSlackToken(), "content" => $fileContent, "filetype" => $filetype, "filename" => $fileName, "title" => $title, "initial_comment" => $comment, "channels" => $this->getChannelIdByName($channel));

			$ch = curl_init($this->getSlackBaseFileUrl());
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
		} catch (Exception $ex)
		{
			$this->sendSlackMessage($channel, $ex->getMessage());
			$this->log($ex->toString());
		}
	}

	public function getChannelsList()
	{
		try
		{
			$postfields = array("token" => $this->getSlackToken(), "exclude_archived" => 1);

			$ch = curl_init($this->getChannelsListUrl());
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);
			return json_decode($response)->channels;
		} catch (Exception $ex)
		{
			$this->log($ex->toString());
		}
	}

	public function createChannel($name)
	{
		try
		{
			$postfields = array("token" => $this->getSlackToken(), "name" => $name);

			$ch = curl_init($this->getChannelCreateUrl());
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
			return json_decode($result)->channel;
		} catch (Exception $ex)
		{
			$this->log($ex->toString());
		}
	}

	public function archiveChannel($channel)
	{
		try
		{
			$postfields = array("token" => $this->getSlackToken(), "channel" => $this->getChannelIdByName($channel));

			$ch = curl_init($this->getChannelArquiveUrl());
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
			return json_decode($result)->channel;
		} catch (Exception $ex)
		{
			$this->log($ex->toString());
		}
	}

	public function getChannelIdByName($channelName)
	{
		try
		{
			$channels = $this->getChannelsList();

			foreach ($channels as $channel)
			{
				if ($channel->name == strtolower($channelName))
				{
					return $channel->id;
				}
			}
			return null;
		} catch (Exception $ex)
		{
			$this->log($ex->toString());
		}
	}

	public function log($data, $filename = 'FVets_Datavalidate.log')
	{
		try
		{
			return Mage::getModel('core/log_adapter', $filename)->log($data);
		} catch (Exception $ex)
		{
			//let the flow continues
		}
	}
}