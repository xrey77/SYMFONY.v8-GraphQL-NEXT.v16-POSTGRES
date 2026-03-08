'use client'

import { client } from '@/lib/ApolloClient';
import { gql } from '@apollo/client'
import { useEffect, useState, useRef } from 'react';
import { useReactToPrint } from 'react-to-print';
import { 
  Chart as ChartJS, 
  CategoryScale, 
  LinearScale, 
  BarElement, 
  Title, 
  Tooltip, 
  Legend, 
  ChartOptions
} from 'chart.js';
import { Bar } from 'react-chartjs-2';
import { ChartData } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';

const GET_SALESDATA = gql`
  query saledata {
    saledataSales {  
      id
      saleamount
      saledate
    }
  }
  `
interface SalesQueryResponse {
  saledataSales: {
    id: string;
    saleamount: number;
    saledate: string;
  }[];
}

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ChartDataLabels
);


// const options: ChartOptions<'bar'> = {
//   responsive: true,
//   layout: {
//     padding: {
//       top: 40 
//     }
//   },
//   scales: {
//     x: { 
//       ticks: {
//         color: 'black', // bottom labels
//       },
//     },    
//     y: {
//       beginAtZero: true,
//       position: 'left',
//       ticks: {
//         color: 'black',  
//         autoSkip: false,
//         callback: (value: string | number) => 
//           new Intl.NumberFormat('en-US', { 
//             style: 'currency', 
//             currency: 'USD',
//             minimumFractionDigits: 2 
//           }).format(Number(value)),
//       }
//     }    
//   },  
//   plugins: {
//     datalabels: {
//       display: true,
//       color: 'black',
//       anchor: 'end', 
//       align: 'top',
//       offset: 4,
//       font: { weight: 'bold' }, 
//       formatter: (value: number) => {
//         return value.toLocaleString('en-US', {
//           style: 'currency',
//           currency: 'USD',
//           minimumFractionDigits: 0
//         });
//       },                 
//     },    
//     scales: {
//       y: {
//         beginAtZero: true,
//         position: 'left',
//         ticks: {
//           autoSkip: false,          
//           stepSize: 500,
//         }
//       }
//     },    
//     legend: { 
//       position: 'top',
//       labels: {
//         color: 'black' // top legend label color
//       }
//     },    
//     title: { 
//       display: true,
//       text: 'Annual Sales Chart',
//       color: 'black',
//       padding: {
//         top: 10, 
//         bottom: 5
//       },
//       font: {
//         size: 24,
//         family: 'Arial',
//         weight: 'bold',
//       }
//     },
//   },
// };

const options: ChartOptions<'bar'> = {
  responsive: true,
  layout: {
    padding: {
      top: 40 
    }
  },
  scales: {
    x: { 
      ticks: {
        color: 'black',
      },
    },    
    y: {
      beginAtZero: true,
      position: 'left',
      ticks: {
        color: 'black',  
        autoSkip: false,
        stepSize: 500, // Moved from the incorrect plugin section to here
        callback: (value: string | number) => 
          new Intl.NumberFormat('en-US', { 
            style: 'currency', 
            currency: 'USD',
            minimumFractionDigits: 2 
          }).format(Number(value)),
      }
    }    
  },  
  plugins: {
    datalabels: {
      display: true,
      color: 'black',
      anchor: 'end', 
      align: 'top',
      offset: 4,
      font: { weight: 'bold' }, 
      formatter: (value: number) => {
        return value.toLocaleString('en-US', {
          style: 'currency',
          currency: 'USD',
          minimumFractionDigits: 0
        });
      },                 
    },
    // REMOVED THE NESTED 'scales' BLOCK FROM HERE
    legend: { 
      position: 'top',
      labels: {
        color: 'black'
      }
    },    
    title: { 
      display: true,
      text: 'Annual Sales Chart',
      color: 'black',
      padding: {
        top: 10, 
        bottom: 5
      },
      font: {
        size: 24,
        family: 'Arial',
        weight: 'bold',
      }
    },
  },
};

interface SalesData {
  saledate: string;
  saleamount: number;
}

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, ChartDataLabels);

const saleschart = () => {
    const [message, setMessage] = useState<string>('');
    const [mounted, setMounted] = useState(false);

    const chartRef = useRef<HTMLDivElement>(null); 


    const [chartData, setChartData] = useState<ChartData<'bar'>>({
        labels: [],
        datasets: [],
    });
    const [logo, setLogo] = useState<HTMLImageElement | null>(null);

    useEffect(() => {
        const img = new Image();
        img.src = '/images/logo2.png';
        img.onload = () => setLogo(img);

        setMounted(true);
        fetchSalesdata();

    },[]);

    const logoPlugin = {
      id: 'logoPlugin',
      beforeDraw: (chart: any) => {
        if (logo && logo.complete) {
          const { ctx, width } = chart;
          const logoWidth = 140;
          const logoHeight = 40;
          const x = (width - logoWidth) / 2; 
          const y = 10; 
          
          ctx.drawImage(logo, x, y, logoWidth, logoHeight);
        } else if (logo) {
          logo.onload = () => chart.draw();
        }
      }
    };


    const fetchSalesdata = async () => {

        try {

            const { data } = await client.query<SalesQueryResponse>({
            query: GET_SALESDATA
            });

            if (data?.saledataSales) {
                 const sales = data.saledataSales;

                setChartData({
                labels: sales.map(item => 
                    new Date(item.saledate).toLocaleDateString('en-US', { month: 'short' })
                ),        
                datasets: [{
                    label: 'Sales Amount',
                    data: sales.map(item => Number(item.saleamount) || 0),
                    backgroundColor: 'rgba(60, 179, 113, 0.8)',
                }],
                });

            }
        } catch (err: any) {
            alert("error");
            console.log(err.message);
            setMessage(err.message);
            setTimeout(() => {
            setMessage('');
            }, 3000);
        }
    }    


    const handlePrint = useReactToPrint({
        contentRef: chartRef,
        documentTitle: "Sales Chart Report",
    });


  return (
  <div className='container-fluid bg-light top-chart h-100'>
      <div className="print-header bg-white">
        <h1>Sale Report</h1>
        <p>Generated on: {new Date().toLocaleDateString()}</p>
      </div>

      <div ref={chartRef} style={{ padding: '20px' }}>
          {chartData.datasets.length > 0 ? (
            <Bar options={options} data={chartData} plugins={[logoPlugin]} />
          ) : (
            <p className="text-center">Loading chart data...</p>
          )}
      </div>
      <div className='btn-print'>
        <button className="btn btn-success" onClick={() => handlePrint()}>Print Chart</button>
      </div>
      <style>{`
        /* Hide the header by default in the browser */
        .print-header {
          display: none;
          text-align: center;
          margin-bottom: 20px;
        }

        @media print {
          @page {
            margin-top: 50px; 
          }

          /* Show the header only when printing */
          .print-header {
            display: block;
          }

          .container { 
            margin: 0; 
          }

          /* Hide the print button so it doesn't appear on paper */
          button {
            display: none;
          }

          canvas { 
            max-width: 100% !important; 
            height: auto !important; 
          }
        }      
      `}</style>
      </div>
  );
}

export default saleschart