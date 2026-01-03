import { useState } from "react";
import api from "../../utils/axios";

export const useRegisterForm = () => {
  const [name, setName] = useState("");
  const [phone, setPhone] = useState("");
  const [dob, setDob] = useState("");
  const [type, setType] = useState("birthday");

  const [link, setLink] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const [showPopup, setShowPopup] = useState(false);
  const [showTerms, setShowTerms] = useState(false);
  const [agreedToTerms, setAgreedToTerms] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);

    if (!agreedToTerms) {
      setError("Please agree to the Terms and Conditions");
      return;
    }

    setLoading(true);
    setLink(null);

    try {
      const dobOrAnniversary = `${type}:${dob}`;
      const res = await api.post("/create-uid", {
        name,
        phone,
        dobOrAnniversary,
      });

      setLink(res.data.link);
      setShowPopup(true);
    } catch (err: any) {
      const msg = err?.response?.data?.msg || "Failed to create link";
      setError(msg);
    } finally {
      setLoading(false);
    }
  };

  const openTermsModal = () => {
    setShowTerms(true);
  };

  const handleTermsUnderstood = () => {
    setShowTerms(false);
    setAgreedToTerms(true);
  };

  const handleTermsCancel = () => {
    setShowTerms(false);
  };

  const closePopup = () => {
    setShowPopup(false);
    setLink(null);
  };

  return {
    // values
    name,
    phone,
    dob,
    type,
    link,
    loading,
    error,
    showPopup,
    showTerms,
    agreedToTerms,

    // setters
    setName,
    setPhone,
    setDob,
    setType,
    setAgreedToTerms,

    // handlers
    handleSubmit,
    openTermsModal,
    handleTermsUnderstood,
    handleTermsCancel,
    closePopup,
  };
};
