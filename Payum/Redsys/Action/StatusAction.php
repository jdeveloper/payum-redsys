<?php
namespace Payum\Redsys\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Offline\Constants;
use Payum\Core\Request\GetBinaryStatus;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetStatusInterface;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request GetStatusInterface */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
  
        if (false == $model['Ds_Response']) {
            $request->markNew();
            return;
        }

	$fp = fopen ( '../ezpublish/logs/tpv-status.log', 'a' );
	fwrite( $fp, print_r( $model, true ) );
	fclose( $fp );

        if (180 == $model['Ds_Response']) {

            $request->markCaptured();

            return;
        }

        if (Constants::STATUS_CAPTURED == $model[Constants::FIELD_STATUS]) {
            $request->markCaptured();

            return;
        }

        $request->markUnknown();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
