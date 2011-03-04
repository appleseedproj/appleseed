<?php


class cTestTestController extends cController {

	public function Display ( $pView = null, $pData = null ) {

		$View = $this->GetView ( $pView );

		$View->Display();

		return ( true );
	}
}


?>
