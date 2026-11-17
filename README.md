# omnipay-btipay
omnipay driver for BT IPay from Banca Transilvania

## Card-on-file (COF)

The gateway supports BT iPay's COF operations using the authenticated REST API.
COF API calls use the recommended HTTP Basic authorization header; the existing
checkout flow continues to use the original BT iPay client.

- `createCard()` registers a zero-value card verification order. Supply `clientId`,
  `orderId`, `returnUrl`, and the customer's `card`; redirect the customer to the
  response URL to complete card verification. To save a card during a payment,
  pass `clientId` to `purchase()`; the usual checkout flow is preserved otherwise.
- `purchaseWithSavedCard()` starts a customer-initiated payment with `clientId`
  and `bindingId`. Optional `scaExemption` values are `TRA`, `LVP`, or `SCP`.
- `merchantInitiatedPurchase()` registers a payment with the `MIT` exemption and
  submits it through `paymentOrderBinding.do`. Supply `clientId` and `bindingId`.
- `getBindings()` lists saved cards and supports `showExpired` and
  `includeCardArt`; the response exposes the returned `bindings` array.
- `bindCard()` / `unbindCard()` reactivate or deactivate a saved card by
  `bindingId`. `getOrderStatusExtended()` retrieves transaction details, including
  `bindingInfo` when available.
- `paymentOrderBinding()` exposes the binding payment endpoint for an order that
  has already been registered.

Use the gateway's `testMode` setting to select the BT iPay sandbox or production
endpoint. COF must be enabled for the merchant by Banca Transilvania. Obtain and
record the customer's consent before sending a `clientId` to save a card, and only
use SCA exemptions agreed with the bank.
