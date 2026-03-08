'use client';

import { useEffect, useState } from 'react';
import { pdf } from '@react-pdf/renderer';
import { client } from '@/lib/ApolloClient';
import { gql } from '@apollo/client';
import { ReportTemplate } from '../components/ReportTemplate';

const GET_REPORTDATA = gql`
  query reportdata {
    reportdataProducts {
      id
      descriptions
      qty
      unit
      costprice
      sellprice
    }
  }
`;

interface Product {
      id: number,
      descriptions: string,
      qty: number,
      unit: string,
      sellprice: number
}

interface PdfReportResponse {
  reportdataProducts: Product[]; 
}

const PdfReport = () => {
  const [pdfUrl, setPdfUrl] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  const fetchAndGenerate = async () => {
    try {
      const { data } = await client.query<PdfReportResponse>({ query: GET_REPORTDATA });
      const products = data?.reportdataProducts || [];

      if (products.length > 0) {
        const doc = <ReportTemplate products={products} />;
        const blob = await pdf(doc).toBlob();
        const url = URL.createObjectURL(blob);
        setPdfUrl(url);
      }
    } catch (err) {
      console.error("PDF Generation Error:", err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchAndGenerate();
    return () => { if (pdfUrl) URL.revokeObjectURL(pdfUrl); };
  }, []);

  return (
    <div className='container-fluid bg-dark vh-100 d-flex flex-column'>
      <div className='flex-grow-1 bg-white m-3 rounded overflow-hidden'>
        {loading ? (
          <div className="d-flex justify-content-center align-items-center h-100">
            Loading Data & Generating PDF...
          </div>
        ) : (
          pdfUrl && <iframe src={`${pdfUrl}#toolbar=1`} width="100%" height="100%" />
        )}
      </div>
    </div>
  );
};

export default PdfReport;
