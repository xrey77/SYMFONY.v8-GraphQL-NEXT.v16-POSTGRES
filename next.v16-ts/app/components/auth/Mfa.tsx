'use client'
import React, { useState, ChangeEvent } from "react"
import { client } from '@/lib/ApolloClient';
import { gql } from '@apollo/client'

const VERIFY_OTP = gql`
  mutation verifyOtp($id: ID!, $otp: String!) {
    verifyOtpUser(input: {id: $id, otp: $otp})  {
      user {
        id
        username
        message
      }
    }
  }
  `

interface MfaResponse {
    verifyOtpUser: {
        user: {
            username: string,
            message: string
        }
    }
}

export default function Mfa() {
    const [otpcode, setOtpcode] = useState<string>('');
    const [message, setMessage] = useState<string>('');
    const [dizable, setDizable] = useState<boolean>(false);
    
    const closeMFA = () => {
        window.sessionStorage.clear();
        window.location.href="/";
    }

    const submitMFA = async (e: ChangeEvent<HTMLFormElement>) => {
        e.preventDefault();         
        setDizable(true);
        const idno = window.sessionStorage.getItem('USERID')?.toString();
        const token = window.sessionStorage.getItem('TOKEN')?.toString();

        try {

            const { data } = await client.mutate<MfaResponse>({
            mutation: VERIFY_OTP,
            variables: { 
                id: idno,
                otp: otpcode
            },
            context: {
            headers: {
                authorization: `Bearer ${token}`,
            },
            },          
            });
            if (data?.verifyOtpUser) {
                setMessage(data.verifyOtpUser.user.message);
                sessionStorage.setItem("USERNAME", data.verifyOtpUser.user.username);
                setTimeout(() => { 
                    setMessage(''); 
                    window.location.reload();
                    setDizable(false);
                }, 3000);
            }
        } catch (err: any) {
            setMessage(err.message);
            setTimeout(() => { 
                setDizable(false);
                setMessage('');
                setOtpcode('');
            }, 3000);
        }    
    }

    return (
    <div className="modal fade" id="staticMFA" data-bs-backdrop="static" data-bs-keyboard="false" tabIndex={-1} aria-labelledby="staticMFALabel" aria-hidden="true">
    <div className="modal-dialog modal-sm modal-dialog-centered">
        <div className="modal-content">
        <div className="modal-header bg-warning">
            <h1 className="modal-title fs-5 text-dark" id="staticMFALabel">2-Factor Authenticator</h1>
            <button onClick={closeMFA} type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div className="modal-body">
            <form onSubmit={submitMFA}>
                <div className="mb-3">
                    <input disabled={dizable} type="text" required className="form-control" value={otpcode} onChange={e => setOtpcode(e.target.value)} autoComplete='off' placeholder="enter OTP Code"/>
                </div>            

                <button disabled={dizable} type="submit" className="btn btn-warning">submit</button>
            </form>
        </div>
        <div className="modal-footer">
            <div id="MfaMsg" className="w-100 text-left text-danger">{message}</div>
        </div>
        </div>
    </div>
    </div>
    );
}