<?php
class InternalTerminal
{
	//----------------------------------------------------------------------------------------------------
	//
	// Yazar      : Ozan UYKUN <ozanbote@windowslive.com> | <ozanbote@gmail.com>
	// Site       : www.zntr.net
	// Lisans     : The MIT License
	// Telif Hakkı: Copyright (c) 2012-2016, zntr.net
	//
	//----------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------------
	// Construct
	//----------------------------------------------------------------------------------------------------
	//
	// @param  void
	// @return bool
	//
	//----------------------------------------------------------------------------------------------------
	public function __construct()
	{
		$this->config = Config::get('ViewObjects', 'terminal');
	}


	/******************************************************************************************
	* PROTECTED CLEAR COMMAND                                                                 *
	*******************************************************************************************
	| Genel Kullanım: Oturum verilerini sıfılar.			  	                  		 	  |
	******************************************************************************************/
	protected function clearCommand()
	{
		unset($_SESSION['persistCommands']);
		unset($_SESSION['commandResponses']);
		unset($_SESSION['commands']);
	}

	/******************************************************************************************
	* RUN                                                                                     *
	*******************************************************************************************
	| Genel Kullanım: Terminali çalıştırır.					 	 						      |
	|															                              |
	| Parametreler: 2 parametresi vardır.                                                     |
	| 1. string var @terminalType => php , cmd.											      |
	| 2. array var @settings => Terminal ayarları.						 				      |
	|          																				  |
	| Örnek Kullanım: Terminal::run('cmd');        	  										  |
	|          																				  |
	******************************************************************************************/
	protected function _run()
	{
		$configs = $this->config;

		$settings['width'] 		=  $configs['width'];
		$settings['height'] 	=  $configs['height'];
		$settings['bgColor'] 	=  $configs['bgColor'];
		$settings['barBgColor'] =  $configs['barBgColor'];
		$settings['textColor'] 	=  $configs['textColor'];
		$settings['textType'] 	=  $configs['textType'];
		$settings['textSize'] 	=  $configs['textSize'];

		if( Method::post('clear') === 'clear' )
		{
			$this->clearCommand();
		}

		if( ! isset($_SESSION['persistCommands']) || ! isset($_SESSION['commands']) )
		{
			$_SESSION['persistCommands']  = [];
			$_SESSION['commands'] 		  = [];
			$_SESSION['commandResponses'] = [];
		}

		$togglingPersist = false;

		if( isset($_POST['persistCommandId']) && is_numeric($_POST['persistCommandId']) )
		{
			$togglingPersist  = true;
			$persistCommandId = $_POST['persistCommandId'];

			if( count($_SESSION['persistCommands']) == $persistCommandId )
			{
				$togglingCurrentPersistCommand = true;
			}
		}

		$previousCommands = '';
		$response = array();

		if( ! empty($_SESSION['persistCommands']) ) foreach( $_SESSION['persistCommands'] as $index => $persist )
		{
			if( ! empty($persist) )
			{
				$currentCommand = $_SESSION['commands'][$index];

				if( ! empty($currentCommand) )
				{
					$previousCommands .= $currentCommand.'; ';
				}
			}
		}

		if( isset($_POST['command']) )
		{
			$command = $_POST['command'];

			if( ! empty($command) && empty($togglingPersist) )
			{
				if( $command === 'clear' )
				{
					$this->clearCommand();
				}
				else
				{
					if( $command !== 'clear' )
					{
						$previousCommands = prefix($previousCommands, 'php zerocore project-name ' . SELECT_PROJECT. ' ');
					}

					exec($previousCommands.$command.' 2>&1', $response);
				}
			}

			if( $command !== 'clear' )
			{
				if( ! empty($togglingPersist) )
				{
					if( $togglingCurrentPersistCommand )
					{
						array_push($_SESSION['persistCommands'], true);
						array_push($_SESSION['commands'], $command);
						array_push($_SESSION['commandResponses'], $response);

						if( ! empty($command) )
						{
							$previousCommands = $previousCommands.$command.'; ';
						}
					}
				}
				else
				{
					array_push($_SESSION['persistCommands'], false);
					array_push($_SESSION['commands'], $command);
					array_push($_SESSION['commandResponses'], $response);
				}
			}
		}
	}

	public function create()
	{
		$this->_run('cmd');
		$this->_terminal($this->config);
	}

	public function _terminal($settings)
	{
		?>
			<style type="text/css">
				*
				{
					margin: 0;
					padding: 0;
				}

				input
				{
					color: inherit;
					font-family: inherit;
					font-size: inherit;
					font-weight: inherit;
					background-color: inherit;
					border: inherit;
				}
				.content
				{
					width: <?php echo $settings['width']; ?>;
					min-width: 400px;
					margin: 0px auto;
					text-align: left;
					overflow: auto;
					background-color: <?php echo $settings['bgColor']; ?>;
					color: <?php echo $settings['textColor']; ?>;
					font-family: <?php echo $settings['textType']; ?>;
					font-weight: bold;
					font-size: <?php echo $settings['textSize']; ?>;
				}
				.terminal
				{
					border: 1px solid #CCC;
					height: <?php echo $settings['height']; ?>;
					position: relative;
					overflow: auto;
					padding-bottom: 20px;
				}
				.terminal .bar
				{
					background:<?php echo $settings['barBgColor']; ?>;;
					height:40px;
					padding: 2px;
					white-space: nowrap;
					overflow: hidden;
					color:<?php echo $settings['textColor']; ?>;
					text-align:center;
					padding-top:12px;
				}
				.terminal .commands
				{
					padding: 2px;
					padding-right: 0;
				}
				.terminal #command
				{
					width: 90%;
					outline:none;
					border:none;
				}

				pre{
					background: none;
					border:none;
					line-height: 0px;
					margin:0;
				}
			</style>

			<div class="content">
				<div class="terminal" onclick="document.getElementById('command').focus();" id="terminal">
					<div class="bar">
						<?php echo 'ZN Framework Terminal Application '; ?>
					</div>

					<form action="<?php echo currentUrl(); ?>" method="post" class="commands" id="commands">

						<?php if( ! empty($_SESSION['commands'])): ?>
						<div>
							<?php foreach ($_SESSION['commands'] as $index => $command): ?>

							<pre><?php echo 'zerocore > ', $command, "\n"; ?></pre>

								<?php foreach ($_SESSION['commandResponses'][$index] as $value):?>
	                                		<pre><?php echo htmlentities($value), "\n"; ?></pre>
								<?php endforeach; ?>

							<?php endforeach; ?>
						</div>
						<?php endif; ?>
						<?php echo 'zerocore > ';?>
						<input type="text" name="command" id="command" autocomplete="off" onkeydown="return commandKeyedDown(event);" />

					</form>
				</div>
			</div>

			<script type="text/javascript">

				<?php
					$singleQuoteCancelledCommands = array();

					if( ! empty( $_SESSION['commands'] ) )
					{
						foreach( $_SESSION['commands'] as $command )
						{
							$cancelledCommand = str_replace('\\', '\\\\', $command);
							$cancelledCommand = str_replace('\'', '\\\'', $command);
							$singleQuoteCancelledCommands[] = $cancelledCommand;
						}
					}
				?>

				var previousCommands = ['', '<?php echo implode('\', \'', $singleQuoteCancelledCommands) ?>', ''];

				var currentCommandIndex = previousCommands.length - 1;

				document.getElementById('command').select();

				document.getElementById('terminal').scrollTop = document.getElementById('terminal').scrollHeight;

				function togglePersistCommand(commandId)
				{
					document.getElementById('persistCommandId').value = commandId;
					document.getElementById('commands').submit();
				}

				function commandKeyedDown(event)
				{
					var keyCode = getKeyCode(event);
					if( keyCode == 38 )
					{
						fillInPreviousCommand();
					}
					else if( keyCode == 40 )
					{
						fillInNextCommand();
					}
					else if( keyCode == 13 )
					{
						if (event.shiftKey)
						{
							togglePersistCommand
							(
								<?php
								if( isset($_SESSION['commands']) )
								{
									echo count($_SESSION['commands']);
								}
								else
								{
									echo 0;
								}
								?>
							);
							return false;
						}
					}
					return true;
				}

				function fillInPreviousCommand()
				{
					currentCommandIndex--;

					if( currentCommandIndex < 0 )
					{
						currentCommandIndex = 0;
						return;
					}
					document.getElementById('command').value = previousCommands[currentCommandIndex];
				}

				function fillInNextCommand()
				{
					currentCommandIndex++;

					if( currentCommandIndex >= previousCommands.length )
					{
						currentCommandIndex = previousCommands.length - 1;
						return;
					}
					document.getElementById('command').value = previousCommands[currentCommandIndex];
				}

				function getKeyCode(event)
				{
					var eventKeyCode = event.keyCode;
					return eventKeyCode;
				}
			</script>
	    <?php
	}
}
