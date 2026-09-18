<?php

namespace App\Enums;

enum StockMovementType: string
{
    case PURCHASE = 'purchase';
    case SALE = 'sale';

    case TRANSFER_IN = 'transfer_in';
    case TRANSFER_OUT = 'transfer_out';

    case SALE_RETURN = 'sale_return';
    case PURCHASE_RETURN = 'purchase_return';

    case SALE_REVERSAL = 'sale_reversal';
    case PURCHASE_REVERSAL = 'purchase_reversal';

    case DAMAGE = 'damage';

    case ADJUSTMENT_IN = 'adjustment_in';
    case ADJUSTMENT_OUT = 'adjustment_out';
}