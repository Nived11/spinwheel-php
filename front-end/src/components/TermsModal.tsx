import React from 'react';
import { X, AlertCircle } from 'lucide-react';

interface TermsModalProps {
  isOpen: boolean;
  onAccept: () => void;
  onCancel: () => void;
}

const TermsModal: React.FC<TermsModalProps> = ({ isOpen, onAccept, onCancel }) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black/80 bg-opacity-20 flex items-center justify-center z-50 p-2 sm:p-4">
      <div className="bg-white rounded-2xl max-w-2xl w-full max-h-[100vh] overflow-hidden flex flex-col shadow-2xl">
        
        {/* Header */}
        <div className="bg-gradient-to-r from-amber-800 via-amber-900 to-slate-800 text-white p-6 relative">
          <button
            onClick={onCancel}
            className="absolute top-4 right-4 text-white hover:text-gray-200 transition-colors"
          >
            <X className="w-6 h-6" />
          </button>
          <h2 className="text-2xl font-bold text-center">Terms & Conditions</h2>
          <p className="text-sm text-center mt-2 opacity-90">
            Please read carefully before participating
          </p>
        </div>

        {/* Content - Scrollable */}
        <div className="p-6 overflow-y-auto flex-1">
          <div className="space-y-4 text-gray-700">
            <p className="font-semibold text-lg text-gray-900 mb-4">
              Spin Wheel Promotion - Terms & Conditions
            </p>

            <div className="space-y-3 text-sm leading-relaxed">
              <p><span className="font-semibold">1.</span> The Spin Wheel promotion is organized by Empire Plaza and is valid for a limited period only.</p>
              <p><span className="font-semibold">2.</span> Participation is open to walk-in customers of Empire Plaza. One spin per customer per visit is permitted.</p>
              <p><span className="font-semibold">3.</span> Customers must participate by scanning the official QR code provided by Empire Plaza.</p>
              <p><span className="font-semibold">4.</span> Prizes are randomly assigned and cannot be exchanged for cash, transferred, or substituted.</p>
              <p><span className="font-semibold">5.</span> To redeem any prize, the customer must use the same mobile phone number that was submitted while participating in the Spin Wheel.</p>
              <p><span className="font-semibold">6.</span> For verification purposes, the Empire Plaza team will place a call to the customer's registered phone number in front of the customer at the restaurant.</p>
              <p><span className="font-semibold">7.</span> Prize redemption will be approved only if the call successfully reaches the customer's phone, confirming that the winning number matches the number provided during participation.</p>
              <p><span className="font-semibold">8.</span> Prize redemption will be processed only when the winning mobile phone and phone number are physically present with the customer at Empire Plaza.</p>
              <p><span className="font-semibold">9.</span> The Free Full Mandi prize is valid for dine-in only and is subject to availability.</p>
              <p><span className="font-semibold">10.</span> All prizes must be redeemed on the same day of winning, unless otherwise specified by Empire Plaza staff.</p>
              <p><span className="font-semibold">11.</span> Empire Plaza reserves the right to verify the winning screen, confirmation message, call log, or any related details before approving redemption.</p>
              <p><span className="font-semibold">12.</span> Any attempt to share, forward, duplicate, or misuse the prize, winning link, or phone number will result in immediate disqualification.</p>
              <p><span className="font-semibold">13.</span> Empire Plaza reserves the right to refuse redemption in cases of suspected fraud, technical misuse, or violation of these Terms & Conditions.</p>
              <p><span className="font-semibold">14.</span> Empire Plaza reserves the right to modify, suspend, or cancel the promotion at any time without prior notice.</p>
              <p><span className="font-semibold">15.</span> All decisions taken by Empire Plaza regarding the Spin Wheel promotion shall be final and binding.</p>
              <p><span className="font-semibold">16.</span> By participating, customers consent to basic data usage strictly for verification and redemption purposes only.</p>
            </div>

            <div className="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded flex gap-3">
              <AlertCircle className="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" />
              <p className="text-sm text-yellow-800">
                <span className="font-semibold">Important:</span> By clicking "Accept & Continue", you acknowledge that you have read, understood, and agree to these Terms & Conditions.
              </p>
            </div>
          </div>
        </div>

        {/* Footer - Buttons */}
<div className="bg-gray-50 px-6 py-4 flex gap-3 border-t">
  <button
    onClick={onCancel}
    className="flex-1 px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400 transition-colors"
  >
    Cancel
  </button>
  <button
    onClick={onAccept}
    className="flex-1 px-6 py-3 bg-gradient-to-r from-amber-800 via-amber-900 to-slate-800 text-white rounded-lg font-semibold hover:from-amber-900 hover:via-slate-800 hover:to-slate-900 transition-all"
  >
    I Understood
  </button>
</div>

      </div>
    </div>
  );
};

export default TermsModal;
