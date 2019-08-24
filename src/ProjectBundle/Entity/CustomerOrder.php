<?php

namespace ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;


/**
 * CustomerOrder
 *
 * @ORM\Table(name="customer_order",
 *          indexes={@ORM\Index(name="customer_order_order_number_1", columns={"order_number"}),
 *                   @ORM\Index(name="customer_order_order_paid_1", columns={"paid"}),
 *                   @ORM\Index(name="customer_order_order_fulfilled_1", columns={"fulfilled"}),
 *                   @ORM\Index(name="customer_order_order_cancelled_1", columns={"cancelled"}),
 *                   @ORM\Index(name="customer_order_order_refunded_1", columns={"refunded"}),
 *                   @ORM\Index(name="customer_order_order_deleted_1", columns={"deleted"}),
 *                   @ORM\Index(name="customer_order_order_payment_option_1", columns={"payment_option"}),
 *                   @ORM\Index(name="customer_order_order_is_read_1", columns={"is_read"})})
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\CustomerOrderRepository")
 */
class CustomerOrder
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="User", inversedBy="customerOrders")
    * @ORM\JoinColumn(name="fos_user_id", referencedColumnName="id", onDelete="CASCADE")
    */
    private $user;

    /**
     * @ORM\Column(name="order_number", type="string", length=45)
     */
    private $orderNumber;

    /**
    * @ORM\Column(name="order_date", type="datetime")
    */
    private $orderDate;

    /**
    * @ORM\Column(name="ship_date", type="date", nullable = true)
    */
    private $shipDate;

    /**
     * @ORM\Column(name="item_count", type="integer", nullable = true)
     */
    private $itemCount;

    /**
	 * @ORM\Column(name="shipping_cost", type="decimal", scale=2, nullable = true)
	 */
	private $shippingCost;

    /**
	 * @ORM\Column(name="sub_total", type="decimal", scale=2, nullable = true)
	 */
	private $subTotal;

    /**
	 * @ORM\Column(name="discount_code", type="string", length=255, nullable = true)
	 */
	private $discountCode;

    /**
	 * @ORM\Column(name="discount_amount", type="decimal", scale=2, nullable = true)
	 */
	private $discountAmount;

    /**
	 * @ORM\Column(name="total_price", type="decimal", scale=2, nullable = true)
	 */
	private $totalPrice;

    /**
     * @ORM\Column(name="payment_option", type="string", length=255)
     */
    private $paymentOption;

    /**
     * @ORM\Column(name="payment_option_title", type="string", length=255)
     */
    private $paymentOptionTitle;

    /** @ORM\Column(name="paid", type="smallint", options={"unsigned":true, "default":0}) */
    private $paid = 0;

    /** @ORM\Column(name="fulfilled", type="smallint", options={"unsigned":true, "default":0}) */
    private $fulfilled = 0;

    /** @ORM\Column(name="cancelled", type="smallint", options={"unsigned":true, "default":0}) */
    private $cancelled = 0;

    /** @ORM\Column(name="refunded", type="smallint", options={"unsigned":true, "default":0}) */
    private $refunded = 0;

    /** @ORM\Column(name="deleted", type="smallint", options={"unsigned":true, "default":0}) */
    private $deleted = 0;

    /**
     * @ORM\OneToMany(targetEntity="CustomerOrderItem", mappedBy="customerOrder")
     */
    private $customerOrderItems;

    /**
     * @ORM\OneToMany(targetEntity="CustomerPaymentBankTransfer", mappedBy="customerOrder")
     */
    private $customerPaymentBankTransfers;

    /**
     * @ORM\OneToMany(targetEntity="CustomerOrderDelivery", mappedBy="customerOrder")
     */
    private $customerOrderDeliverys;

    /**
     * @ORM\OneToMany(targetEntity="TrackingNumber", mappedBy="customerOrder")
     */
    private $trackingNumbers;

    /**
     * @ORM\OneToOne(targetEntity="CustomerPaymentEpayment", mappedBy="customerOrder")
     */
    private $customerPaymentEpayment;

    /**
     * Many CustomerOrder have Many Discounts.
     * @ORM\ManyToMany(targetEntity="Discount", inversedBy="customerOrders")
     * @ORM\JoinTable(name="customer_orders_discounts")
     */
    private $discounts;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable = true)
     */
    private $note;

    /** @ORM\Column(name="is_read", type="smallint", options={"unsigned":true, "default":0}) */
    private $isRead = 0;

    /**
     * @ORM\Column(name="direction_distance", type="integer", nullable = true)
     */
    private $directionDistance;

    /**
     * @ORM\Column(name="direction_distance_text", type="string", length=255, nullable = true)
     */
    private $directionDistanceText;

    /**
     * @ORM\Column(name="direction_origin_showroom_id", type="integer", nullable = true)
     */
    private $directionOriginShowroomId;

    /**
     * @ORM\Column(name="direction_origin_lat_lng", type="string", length=255, nullable = true)
     */
    private $directionOriginLatLng;

    /**
     * @ORM\Column(name="direction_origin_showroom_name", type="string", length=255, nullable = true)
     */
    private $directionOriginShowroomName;

    /**
     * @ORM\Column(name="direction_destination_delivery_address_id", type="integer", nullable = true)
     */
    private $directionDestinationDeliveryAddressId;

    /**
     * @ORM\Column(name="direction_destination_lat_lng", type="string", length=255, nullable = true)
     */
    private $directionDestinationLatLng;

    /**
	 * @ORM\Column(name="shipping_cost_by_distance", type="decimal", scale=2, nullable = true)
	 */
	private $shippingCostByDistance;

    public function __construct()
    {
        $this->customerOrderItems = new ArrayCollection();
        $this->customerPaymentBankTransfers = new ArrayCollection();
        $this->customerOrderDeliverys = new ArrayCollection();
        $this->trackingNumbers = new ArrayCollection();
        $this->discounts = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of Id
     *
     * @param int id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of User
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of User
     *
     * @param mixed user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of Order Number
     *
     * @return mixed
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set the value of Order Number
     *
     * @param mixed orderNumber
     *
     * @return self
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get the value of Order Date
     *
     * @return mixed
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    /**
     * Set the value of Order Date
     *
     * @param mixed orderDate
     *
     * @return self
     */
    public function setOrderDate($orderDate)
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    /**
     * Get the value of Ship Date
     *
     * @return mixed
     */
    public function getShipDate()
    {
        return $this->shipDate;
    }

    /**
     * Set the value of Ship Date
     *
     * @param mixed shipDate
     *
     * @return self
     */
    public function setShipDate($shipDate)
    {
        $this->shipDate = $shipDate;

        return $this;
    }

    /**
     * Get the value of Item Count
     *
     * @return mixed
     */
    public function getItemCount()
    {
        return $this->itemCount;
    }

    /**
     * Set the value of Item Count
     *
     * @param mixed itemCount
     *
     * @return self
     */
    public function setItemCount($itemCount)
    {
        $this->itemCount = $itemCount;

        return $this;
    }

    /**
     * Get the value of Shipping Cost
     *
     * @return mixed
     */
    public function getShippingCost()
    {
        return $this->shippingCost;
    }

    /**
     * Set the value of Shipping Cost
     *
     * @param mixed shippingCost
     *
     * @return self
     */
    public function setShippingCost($shippingCost)
    {
        $this->shippingCost = $shippingCost;

        return $this;
    }

    /**
     * Get the value of Sub Total
     *
     * @return mixed
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * Set the value of Sub Total
     *
     * @param mixed subTotal
     *
     * @return self
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    /**
     * Get the value of Discount Code
     *
     * @return mixed
     */
    public function getDiscountCode()
    {
        return $this->discountCode;
    }

    /**
     * Set the value of Discount Code
     *
     * @param mixed discountCode
     *
     * @return self
     */
    public function setDiscountCode($discountCode)
    {
        $this->discountCode = $discountCode;

        return $this;
    }

    /**
     * Get the value of Discount Amount
     *
     * @return mixed
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * Set the value of Discount Amount
     *
     * @param mixed discountAmount
     *
     * @return self
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * Get the value of Total Price
     *
     * @return mixed
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * Set the value of Total Price
     *
     * @param mixed totalPrice
     *
     * @return self
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get the value of Payment Option
     *
     * @return mixed
     */
    public function getPaymentOption()
    {
        return $this->paymentOption;
    }

    /**
     * Set the value of Payment Option
     *
     * @param mixed paymentOption
     *
     * @return self
     */
    public function setPaymentOption($paymentOption)
    {
        $this->paymentOption = $paymentOption;

        return $this;
    }

    /**
     * Get the value of Payment Option Title
     *
     * @return mixed
     */
    public function getPaymentOptionTitle()
    {
        return $this->paymentOptionTitle;
    }

    /**
     * Set the value of Payment Option Title
     *
     * @param mixed paymentOptionTitle
     *
     * @return self
     */
    public function setPaymentOptionTitle($paymentOptionTitle)
    {
        $this->paymentOptionTitle = $paymentOptionTitle;

        return $this;
    }

    /**
     * Get the value of Paid
     *
     * @return mixed
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Set the value of Paid
     *
     * @param mixed paid
     *
     * @return self
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Get the value of Fulfilled
     *
     * @return mixed
     */
    public function getFulfilled()
    {
        return $this->fulfilled;
    }

    /**
     * Set the value of Fulfilled
     *
     * @param mixed fulfilled
     *
     * @return self
     */
    public function setFulfilled($fulfilled)
    {
        $this->fulfilled = $fulfilled;

        return $this;
    }

    /**
     * Get the value of Cancelled
     *
     * @return mixed
     */
    public function getCancelled()
    {
        return $this->cancelled;
    }

    /**
     * Set the value of Cancelled
     *
     * @param mixed cancelled
     *
     * @return self
     */
    public function setCancelled($cancelled)
    {
        $this->cancelled = $cancelled;

        return $this;
    }

    /**
     * Get the value of Deleted
     *
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set the value of Deleted
     *
     * @param mixed deleted
     *
     * @return self
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get the value of Note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set the value of Note
     *
     * @param string note
     *
     * @return self
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }


    /**
     * Get the value of Customer Order Items
     *
     * @return mixed
     */
    public function getCustomerOrderItems()
    {
        return $this->customerOrderItems;
    }

    /**
     * Set the value of Customer Order Items
     *
     * @param mixed customerOrderItems
     *
     * @return self
     */
    public function setCustomerOrderItems($customerOrderItems)
    {
        $this->customerOrderItems = $customerOrderItems;

        return $this;
    }


    /**
     * Get the value of Customer Payment Bank Transfers
     *
     * @return mixed
     */
    public function getCustomerPaymentBankTransfers()
    {
        return $this->customerPaymentBankTransfers;
    }

    /**
     * Set the value of Customer Payment Bank Transfers
     *
     * @param mixed customerPaymentBankTransfers
     *
     * @return self
     */
    public function setCustomerPaymentBankTransfers($customerPaymentBankTransfers)
    {
        $this->customerPaymentBankTransfers = $customerPaymentBankTransfers;

        return $this;
    }


    /**
     * Get the value of Customer Payment Epayment
     *
     * @return mixed
     */
    public function getCustomerPaymentEpayment()
    {
        return $this->customerPaymentEpayment;
    }

    /**
     * Set the value of Customer Payment Epayment
     *
     * @param mixed customerPaymentEpayment
     *
     * @return self
     */
    public function setCustomerPaymentEpayment($customerPaymentEpayment)
    {
        $this->customerPaymentEpayment = $customerPaymentEpayment;

        return $this;
    }


    /**
     * Get the value of Customer Order Deliverys
     *
     * @return mixed
     */
    public function getCustomerOrderDeliverys()
    {
        return $this->customerOrderDeliverys;
    }

    /**
     * Set the value of Customer Order Deliverys
     *
     * @param mixed customerOrderDeliverys
     *
     * @return self
     */
    public function setCustomerOrderDeliverys($customerOrderDeliverys)
    {
        $this->customerOrderDeliverys = $customerOrderDeliverys;

        return $this;
    }


    /**
     * Get the value of Many CustomerOrder have Many Discounts.
     *
     * @return mixed
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * Set the value of Many CustomerOrder have Many Discounts.
     *
     * @param mixed discounts
     *
     * @return self
     */
    public function setDiscounts($discounts)
    {
        $this->discounts = $discounts;

        return $this;
    }


    /**
     * @param mixed discounts
     */
    public function removeDiscounts(Discount $discount)
    {
        if (false === $this->discounts->contains($discount)) {
            return;
        }
        $this->discounts->removeElement($discount);
        $discount->removeCustomerOrders($this);
    }

	/**
     * @param mixed discounts
     */
    public function addDiscounts(Discount $discount)
    {
        if (true === $this->discounts->contains($discount)) {
            return;
        }
        $this->discounts->add($discount);
        $discount->addCustomerOrders($this);
    }


    /**
     * Get the value of Refunded
     *
     * @return mixed
     */
    public function getRefunded()
    {
        return $this->refunded;
    }

    /**
     * Set the value of Refunded
     *
     * @param mixed refunded
     *
     * @return self
     */
    public function setRefunded($refunded)
    {
        $this->refunded = $refunded;

        return $this;
    }


    /**
     * Get the value of Tracking Numbers
     *
     * @return mixed
     */
    public function getTrackingNumbers()
    {
        return $this->trackingNumbers;
    }

    /**
     * Set the value of Tracking Numbers
     *
     * @param mixed trackingNumbers
     *
     * @return self
     */
    public function setTrackingNumbers($trackingNumbers)
    {
        $this->trackingNumbers = $trackingNumbers;

        return $this;
    }


    /**
     * Get the value of Is Read
     *
     * @return mixed
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * Set the value of Is Read
     *
     * @param mixed isRead
     *
     * @return self
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;

        return $this;
    }


    /**
     * Get the value of Direction Distance
     *
     * @return mixed
     */
    public function getDirectionDistance()
    {
        return $this->directionDistance;
    }

    /**
     * Set the value of Direction Distance
     *
     * @param mixed directionDistance
     *
     * @return self
     */
    public function setDirectionDistance($directionDistance)
    {
        $this->directionDistance = $directionDistance;

        return $this;
    }

    /**
     * Get the value of Direction Distance Text
     *
     * @return mixed
     */
    public function getDirectionDistanceText()
    {
        return $this->directionDistanceText;
    }

    /**
     * Set the value of Direction Distance Text
     *
     * @param mixed directionDistanceText
     *
     * @return self
     */
    public function setDirectionDistanceText($directionDistanceText)
    {
        $this->directionDistanceText = $directionDistanceText;

        return $this;
    }

    /**
     * Get the value of Direction Origin Showroom Id
     *
     * @return mixed
     */
    public function getDirectionOriginShowroomId()
    {
        return $this->directionOriginShowroomId;
    }

    /**
     * Set the value of Direction Origin Showroom Id
     *
     * @param mixed directionOriginShowroomId
     *
     * @return self
     */
    public function setDirectionOriginShowroomId($directionOriginShowroomId)
    {
        $this->directionOriginShowroomId = $directionOriginShowroomId;

        return $this;
    }

    /**
     * Get the value of Direction Origin Lat Lng
     *
     * @return mixed
     */
    public function getDirectionOriginLatLng()
    {
        return $this->directionOriginLatLng;
    }

    /**
     * Set the value of Direction Origin Lat Lng
     *
     * @param mixed directionOriginLatLng
     *
     * @return self
     */
    public function setDirectionOriginLatLng($directionOriginLatLng)
    {
        $this->directionOriginLatLng = $directionOriginLatLng;

        return $this;
    }

    /**
     * Get the value of Direction Origin Showroom Name
     *
     * @return mixed
     */
    public function getDirectionOriginShowroomName()
    {
        return $this->directionOriginShowroomName;
    }

    /**
     * Set the value of Direction Origin Showroom Name
     *
     * @param mixed directionOriginShowroomName
     *
     * @return self
     */
    public function setDirectionOriginShowroomName($directionOriginShowroomName)
    {
        $this->directionOriginShowroomName = $directionOriginShowroomName;

        return $this;
    }

    /**
     * Get the value of Direction Destination Delivery Address Id
     *
     * @return mixed
     */
    public function getDirectionDestinationDeliveryAddressId()
    {
        return $this->directionDestinationDeliveryAddressId;
    }

    /**
     * Set the value of Direction Destination Delivery Address Id
     *
     * @param mixed directionDestinationDeliveryAddressId
     *
     * @return self
     */
    public function setDirectionDestinationDeliveryAddressId($directionDestinationDeliveryAddressId)
    {
        $this->directionDestinationDeliveryAddressId = $directionDestinationDeliveryAddressId;

        return $this;
    }

    /**
     * Get the value of Direction Destination Lat Lng
     *
     * @return mixed
     */
    public function getDirectionDestinationLatLng()
    {
        return $this->directionDestinationLatLng;
    }

    /**
     * Set the value of Direction Destination Lat Lng
     *
     * @param mixed directionDestinationLatLng
     *
     * @return self
     */
    public function setDirectionDestinationLatLng($directionDestinationLatLng)
    {
        $this->directionDestinationLatLng = $directionDestinationLatLng;

        return $this;
    }


    /**
     * Get the value of Shipping Cost By Distance
     *
     * @return mixed
     */
    public function getShippingCostByDistance()
    {
        return $this->shippingCostByDistance;
    }

    /**
     * Set the value of Shipping Cost By Distance
     *
     * @param mixed shippingCostByDistance
     *
     * @return self
     */
    public function setShippingCostByDistance($shippingCostByDistance)
    {
        $this->shippingCostByDistance = $shippingCostByDistance;

        return $this;
    }

}
